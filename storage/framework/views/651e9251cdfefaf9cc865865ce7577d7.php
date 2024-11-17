<?php echo $__env->make('Chatify::layouts.headLinks', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="messenger">
    <input type="hidden" id="chat_type" value="<?php echo e($type??null); ?>">
    <input type="hidden" id="chat_type_id" value="<?php echo e($paramId??null); ?>">
    
    <div class="messenger-listView <?php echo e(!!$id ? 'conversation-active' : ''); ?>">
        
        <?php
        $messageLabel = get_label('messages', 'MESSAGES');
        if (isset($type)) {
        if ($type == 'project') {
        $messageLabel = get_label('project','Project').':'.$project->title;
        } elseif ($type == 'task') {
        $messageLabel = get_label('task','Task').':'.$task->title;
        }
        }
        ?>
        <div class="m-header">
            <nav>
                <a href="#"><i class="fas fa-inbox"></i> <span class="messenger-headTitle"><?php echo e($messageLabel); ?></span> </a>
                
                <nav class="m-header-right">
                    <a href="#"><i class="fas fa-cog settings-btn"></i></a>
                    <a href="#" class="listView-x"><i class="fas fa-times"></i></a>
                </nav>
            </nav>
            
            <input type="text" class="messenger-search" placeholder="<?= get_label('search', 'Search') ?>" />
            
            
        </div>
        
        <div class="m-body contacts-container">
            
            
            <div class="show messenger-tab users-tab app-scroll" data-view="users">
                
                <div class="favorites-section">
                    <p class="messenger-title"><span><?= get_label('favorites', 'Favorites') ?></span></p>
                    <div class="messenger-favorites app-scroll-hidden"></div>
                </div>
                
                <p class="messenger-title"><span></span></p>
                <?php echo view('Chatify::layouts.listItem', ['get' => 'saved']); ?>

                
                <p class="messenger-title"><span><?= get_label('all_messages', 'All Messages') ?></span></p>
                <div class="listOfContacts" style="width: 100%;height: calc(100% - 272px);position: relative;"></div>
            </div>
            
            <div class="messenger-tab search-tab app-scroll" data-view="search">
                
                <p class="messenger-title"><span><?= get_label('search', 'Search') ?></span></p>
                <div class="search-records">
                    <p class="message-hint center-el"><span><?= get_label('type_to_search', 'Type to search') ?>..</span></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="messenger-messagingView">
        
        <div class="m-header m-header-messaging">
            <nav class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                
                <div class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                    <a href="#" class="show-listView"><i class="fas fa-arrow-left"></i></a>
                    <div class="avatar av-s header-avatar" style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;">
                    </div>
                    <a href="#" class="user-name"><?php echo e(Auth::user()->first_name.' '.Auth::user()->last_name); ?></a>
                </div>
                
          <nav class="m-header-right">
    <a href="#" class="add-to-favorite"><i class="fas fa-star"></i></a>
    <a href="<?php echo e(route('home.index')); ?>" id="home-icon"><i class="fas fa-home"></i></a>
    <a href="#" class="show-infoSide"><i class="fas fa-info-circle"></i></a>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.self !== window.top) {
            // The page is in an iframe
            document.getElementById('home-icon').style.display = 'none';
        }
    });
</script>
            </nav>
            
            <div class="internet-connection">
                <span class="ic-connected"><?= get_label('connected', 'Connected') ?></span>
                <span class="ic-connecting"><?= get_label('connecting', 'Connecting') ?>...</span>
                <span class="ic-noInternet"><?= get_label('no_internet_access', 'No internet access') ?></span>
            </div>
            <?php
            $pusher_settings = get_settings('pusher_settings');
            $is_settings = isset($pusher_settings['pusher_app_id']) &&
                isset($pusher_settings['pusher_app_key']) &&
                isset($pusher_settings['pusher_app_secret']) &&
                isset($pusher_settings['pusher_app_cluster']) &&
                !empty($pusher_settings['pusher_app_id']) &&
                !empty($pusher_settings['pusher_app_key']) &&
                !empty($pusher_settings['pusher_app_secret']) &&
                !empty($pusher_settings['pusher_app_cluster']) ? 1 : 0;
            ?>
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'admin')): ?>
            <?php if($is_settings == 0): ?>
            <div class="settings-alert">
                Important settings for the chat feature are not set. <a href="/settings/pusher" target="_blank">Click here to configure them now.</a>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div class="m-body messages-container app-scroll">
            <div class="messages">
                <p class="message-hint center-el"><span><?= get_label('please_select_a_chat_to_start_messaging', 'Please select a chat to start messaging') ?></span></p>
            </div>
            
            <div class="typing-indicator">
                <div class="message-card typing">
                    <div class="message">
                        <span class="typing-dots">
                            <span class="dot dot-1"></span>
                            <span class="dot dot-2"></span>
                            <span class="dot dot-3"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <?php echo $__env->make('Chatify::layouts.sendForm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    
    <div class="messenger-infoView app-scroll">
        
        <nav>
            <p><?= get_label('user_details', 'User Details') ?></p>
            <a href="#"><i class="fas fa-times"></i></a>
        </nav>
        <?php echo view('Chatify::layouts.info')->render(); ?>

    </div>
</div>
<?php echo $__env->make('Chatify::layouts.modals', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('Chatify::layouts.footerLinks', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH C:\Siddharth Gor\Test\resources\views/vendor/Chatify/pages/app.blade.php ENDPATH**/ ?>