@extends(Config::get('chatter.master_file_extend'))

@section(Config::get('chatter.yields.head'))
 
    <link href="/vendor/devdojo/chatter/assets/css/simplemde.min.css" rel="stylesheet">
    
@stop
<link rel="stylesheet" href="{{url('css/chatter.css')}}">
<link rel="stylesheet" href="{{url('css/simplemde.min.css')}}">
@section('style')
 <style>
     #chatter #chatter_header a.back_btn i{
         position: relative;
         left: -1px;
         top: 8px;
     }
     .py-4{
         padding: 0 !important;
     }
     #chatter ul.discussions li a.discussion_list .chatter_post_actions, #chatter ul.discussions li .chatter_posts .chatter_post_actions{
         position: absolute !important;
       right: unset !important;
         left: 0 !important;
      }
     #chatter .conversation ul.discussions li .chatter_posts .chatter_middle{
          padding-right: 80px !important;
          width: 100%;
         text-align: right !important;
     }
     #chatter .chatter_avatar {
         margin: 5px 15px 5px 5px !important;
         position: absolute !important;
         right: 10px !important;
         left: unset !important;
     }
     #chatter ul.discussions li a.discussion_list .chatter_post_actions, #chatter ul.discussions li .chatter_posts .chatter_post_actions {
         position: absolute;
         right: 0px;
         top: 15px;
     }
     #chatter.discussion #new_response{
         padding-right: 100px !important;
         padding-left: 0 !important;
     }
     #new_discussion{
         text-align: right !important;
     }
     #new_response .chatter_avatar{
         right: 22px !important;
     }
 </style>
@endsection
@section('content')

    <div id="chatter" class="discussion">

        <div id="chatter_header" style="background-color:{{ $discussion->color }}">
            <div class="container">
                <a class="back_btn" href="/{{ Config::get('chatter.routes.home') }}"><i class="chatter-back"></i></a>
                <h1>{{ $discussion->title }}</h1><span class="chatter_head_details">  {{ Config::get('chatter.titles.category') }}<a
                        class="chatter_cat"
                        href="/{{ Config::get('chatter.routes.home') }}/{{ Config::get('chatter.routes.category') }}/{{ $discussion->category->slug }}"
                        style="background-color:{{ $discussion->category->color }}">{{ $discussion->category->name }}</a></span>
            </div>
        </div>

        @if(Session::has('chatter_alert'))
            <div class="chatter-alert alert alert-{{ Session::get('chatter_alert_type') }}">
                <div class="container">
                    <strong><i
                            class="chatter-alert-{{ Session::get('chatter_alert_type') }}"></i> {{ Config::get('chatter.alert_messages.' . Session::get('chatter_alert_type')) }}
                    </strong>
                    {{ Session::get('chatter_alert') }}
                    <i class="chatter-close"></i>
                </div>
            </div>
            <div class="chatter-alert-spacer"></div>
        @endif

        @if (count($errors) > 0)
            <div class="chatter-alert alert alert-danger">
                <div class="container">
                    <p><strong><i class="chatter-alert-danger"></i> {{ Config::get('chatter.alert_messages.danger') }}
                        </strong> Please fix the following errors:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="container margin-top">

            <div class="row">

                <div class="col-md-12">

                    <div class="conversation">
                        <ul class="discussions no-bg" style="display:block;">
                            @foreach($posts as $post)
                                <li data-id="{{ $post->id }}" data-markdown="{{ $post->markdown }}">
		                		<span class="chatter_posts">
		                			@if(!Auth::guest() && (Auth::user()->id == $post->user->id))
                                        <div id="delete_warning_{{ $post->id }}" class="chatter_warning_delete">
		                					<i class="chatter-warning"></i>شما مطمئن هستید میخواهید این نظر را پاک کنید؟
		                					<button class="btn btn-sm btn-danger pull-right delete_response">بله</button>
		                					<button class="btn btn-sm btn-default pull-right">لغو</button>
		                				</div>
                                        @if(Auth::check() && Auth::user()->ticketit_admin)
                                            <div class="chatter_post_actions">
                                                <p class="chatter_delete_btn">
                                                    <i class="chatter-delete"></i> حذف
                                                </p>
                                                <p class="chatter_edit_btn">
                                                    <i class="chatter-edit"></i> ویرایش
                                                </p>
			                			    </div>
                                        @endif
                                    @endif
			                		<div>
                                        <div class="chatter_avatar">
					        			@if(Config::get('chatter.user.avatar_image_database_field'))

                                            <?php $db_field = Config::get('chatter.user.avatar_image_database_field'); ?>

                                            <!-- If the user db field contains http:// or https:// we don't need to use the relative path to the image assets -->
                                                @if( (substr($post->user->{$db_field}, 0, 7) == 'http://') || (substr($post->user->{$db_field}, 0, 8) == 'https://') )
                                                    <img src="{{ $post->user->{$db_field}  }}">
                                                @else
                                                    <img
                                                        src="{{ Config::get('chatter.user.relative_url_to_image_assets') . $post->user->{$db_field}  }}">
                                                @endif

                                            @else
                                                <span class="chatter_avatar_circle"
                                                      style="background-color:#<?= \DevDojo\Chatter\Helpers\ChatterHelper::stringToColorCode($post->user->email) ?>">
					        					{{ ucfirst(substr($post->user->email, 0, 1)) }}
					        				</span>
                                            @endif
					        		</div>

					        		<div class="chatter_middle">
					        			<span class="chatter_middle_details"><a
                                                href="{{ \DevDojo\Chatter\Helpers\ChatterHelper::userLink($post->user) }}">{{ ucfirst($post->user->{Config::get('chatter.user.database_field_with_user_name')}) }}</a> <span
                                                class="ago chatter_middle_details">{{ \Carbon\Carbon::createFromTimeStamp(strtotime($post->created_at))->diffForHumans() }}</span></span>
					        			<div class="chatter_body">

					        				@if($post->markdown)
                                                <pre class="chatter_body_md">{{ $post->body }}</pre>
                                                <?= \DevDojo\Chatter\Helpers\ChatterHelper::demoteHtmlHeaderTags(GrahamCampbell\Markdown\Facades\Markdown::convertToHtml($post->body)); ?>
                                            <!--?= GrahamCampbell\Markdown\Facades\Markdown::convertToHtml( $post->body ); ?-->
                                            @else
                                                <?= $post->body; ?>
                                            @endif

					        			</div>
					        		</div>
                                    </div>

					        		<div class="chatter_clear"></div>
				        		</span>
                                </li>
                            @endforeach


                        </ul>
                    </div>

                    <div id="pagination">{{ $posts->links() }}</div>

                    @if(!Auth::guest())

                        <div id="new_response">

                            <div class="chatter_avatar">
                            @if(Config::get('chatter.user.avatar_image_database_field'))

                                <?php $db_field = Config::get('chatter.user.avatar_image_database_field'); ?>

                                <!-- If the user db field contains http:// or https:// we don't need to use the relative path to the image assets -->
                                    @if( (substr(Auth::user()->{$db_field}, 0, 7) == 'http://') || (substr(Auth::user()->{$db_field}, 0, 8) == 'https://') )
                                        <img src="{{ Auth::user()->{$db_field}  }}">
                                    @else
                                        <img
                                            src="{{ Config::get('chatter.user.relative_url_to_image_assets') . Auth::user()->{$db_field}  }}">
                                    @endif

                                @else
                                    <span class="chatter_avatar_circle"
                                          style="background-color:#<?= \DevDojo\Chatter\Helpers\ChatterHelper::stringToColorCode(Auth::user()->email) ?>">
		        					{{ strtoupper(substr(Auth::user()->email, 0, 1)) }}
		        				</span>
                                @endif
                            </div>

                            <div id="new_discussion">


                                <div class="chatter_loader dark" id="new_discussion_loader">
                                    <div></div>
                                </div>

                                <form id="chatter_form_editor" action="/{{ Config::get('chatter.routes.home') }}/posts"
                                      method="POST">

                                    <!-- BODY -->
                                    <div id="editor">
                                        @if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
                                            <label id="tinymce_placeholder">متن گفت و گو را وارد کنید</label>
                                            <textarea id="body" class="richText" name="body"
                                                      placeholder="">{{ old('body') }}</textarea>
                                        @elseif($chatter_editor == 'simplemde')
                                            <textarea id="simplemde" name="body"
                                                      placeholder="">{{ old('body') }}</textarea>
                                        @endif
                                    </div>

                                    <input type="hidden" name="_token" id="csrf_token_field" value="{{ csrf_token() }}">
                                    <input type="hidden" name="chatter_discussion_id" value="{{ $discussion->id }}">
                                </form>

                            </div><!-- #new_discussion -->
                            <div id="discussion_response_email">
                                <button id="submit_response" class="btn btn-success pull-right"><i
                                        class="chatter-new"></i> ارسال
                                </button>
                                @if(Config::get('chatter.email.enabled'))
                                    <div id="notify_email">
                                        <img src="/vendor/devdojo/chatter/public/assets/images/email.gif"
                                             class="chatter_email_loader">
                                        <!-- Rounded toggle switch -->
                                        <span>به من اطلاع بده</span>
                                        <label class="switch">
                                            <input type="checkbox" id="email_notification"
                                                   name="email_notification" @if(!Auth::guest() && $discussion->users->contains(Auth::user()->id)){{ 'checked' }}@endif>
                                            <span class="on">Yes</span>
                                            <span class="off">No</span>
                                            <div class="slider round"></div>
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @else

                        <div id="login_or_register">

                            <p>لطفا برای ارسال نظر ابتدا <a
                                    href="/{{ Config::get('chatter.routes.home') }}/register">  ثبت نام </a>   یا <a href="/{{ Config::get('chatter.routes.home') }}/login">وارد</a>   سایت شوید </p>
                        </div>

                    @endif

                </div>


            </div>
        </div>

    </div>

    <input type="hidden" id="chatter_tinymce_toolbar" value="{{ Config::get('chatter.tinymce.toolbar') }}">
    <input type="hidden" id="chatter_tinymce_plugins" value="{{ Config::get('chatter.tinymce.plugins') }}">
    <input type="hidden" id="current_path" value="{{ Request::path() }}">

@stop

@section('footer')

    @if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
        <script>var chatter_editor = 'tinymce';</script>
    @elseif($chatter_editor == 'simplemde')
        <script>var chatter_editor = 'simplemde';</script>
    @endif
    <script src="/vendor/devdojo/chatter/public/assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="/vendor/devdojo/chatter/public/assets/js/tinymce.js"></script>
    <script>
        var my_tinymce = tinyMCE;
        $('document').ready(function () {

            $('#tinymce_placeholder').click(function () {
                my_tinymce.activeEditor.focus();
            });

        });
    </script>

    <script src="/vendor/devdojo/chatter/public/assets/js/simplemde.min.js"></script>
    <script src="/vendor/devdojo/chatter/public/assets/js/chatter_simplemde.js"></script>
    <script src="/vendor/devdojo/chatter/public/assets/js/chatter.js"></script>


    <script>
        $('document').ready(function () {

            var simplemdeEditors = [];

            $('.chatter_edit_btn').click(function () {
                parent = $(this).parents('li');
                parent.addClass('editing');
                id = parent.data('id');
                markdown = parent.data('markdown');
                container = parent.find('.chatter_middle');

                if (markdown) {
                    body = container.find('.chatter_body_md');
                } else {
                    body = container.find('.chatter_body');
                    markdown = 0;
                }

                details = container.find('.chatter_middle_details');

                // dynamically create a new text area
                container.prepend('<textarea id="post-edit-' + id + '"></textarea>');
                // Client side XSS fix
                $("#post-edit-" + id).text(body.html());
                container.append('<div class="chatter_update_actions"><button class="btn btn-success pull-right update_chatter_edit"  data-id="' + id + '" data-markdown="' + markdown + '"><i class="chatter-check"></i> Update Response</button><button href="/" class="btn btn-default pull-right cancel_chatter_edit" data-id="' + id + '"  data-markdown="' + markdown + '">Cancel</button></div>');

                // create new editor from text area
                if (markdown) {
                    simplemdeEditors['post-edit-' + id] = newSimpleMde(document.getElementById('post-edit-' + id));
                } else {
                    initializeNewEditor('post-edit-' + id);
                }

            });

            $('.discussions li').on('click', '.cancel_chatter_edit', function (e) {
                post_id = $(e.target).data('id');
                markdown = $(e.target).data('markdown');
                parent_li = $(e.target).parents('li');
                parent_actions = $(e.target).parent('.chatter_update_actions');
                if (!markdown) {
                    tinymce.remove('#post-edit-' + post_id);
                } else {
                    $(e.target).parents('li').find('.editor-toolbar').remove();
                    $(e.target).parents('li').find('.editor-preview-side').remove();
                    $(e.target).parents('li').find('.CodeMirror').remove();
                }

                $('#post-edit-' + post_id).remove();
                parent_actions.remove();

                parent_li.removeClass('editing');
            });

            $('.discussions li').on('click', '.update_chatter_edit', function (e) {
                post_id = $(e.target).data('id');
                markdown = $(e.target).data('markdown');

                if (markdown) {
                    update_body = simplemdeEditors['post-edit-' + post_id].value();
                } else {
                    update_body = tinyMCE.get('post-edit-' + post_id).getContent();
                }

                $.form('/{{ Config::get('chatter.routes.home') }}/posts/' + post_id, {
                    _token: '{{ csrf_token() }}',
                    _method: 'PATCH',
                    'body': update_body
                }, 'POST').submit();
            });

            $('#submit_response').click(function () {
                $('#chatter_form_editor').submit();
            });

            // ******************************
            // DELETE FUNCTIONALITY
            // ******************************

            $('.chatter_delete_btn').click(function () {
                parent = $(this).parents('li');
                parent.addClass('delete_warning');
                id = parent.data('id');
                $('#delete_warning_' + id).addClass('d-flex');
            });

            $('.chatter_warning_delete .btn-default').click(function () {
                $(this).parent('.chatter_warning_delete').removeClass('d-flex');
                $(this).parents('li').removeClass('delete_warning');
            });

            $('.delete_response').click(function () {
                post_id = $(this).parents('li').data('id');
                $.form('/{{ Config::get('chatter.routes.home') }}/posts/' + post_id, {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                }, 'POST').submit();
            });

        });


    </script>

@stop
