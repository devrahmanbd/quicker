
<?php if($get == 'saved'): ?>
<table class="messenger-list-item" data-contact="<?php echo e(Auth::user()->id); ?>">
    <tr data-action="0">
        
        <td>
            <div class="saved-messages avatar av-m">
                <span class="far fa-bookmark"></span>
            </div>
        </td>
        
        <td>
            <p data-id="<?php echo e(Auth::user()->id); ?>" data-type="user"><span><?= get_label('you', 'You') ?></span></p>
            <span><?= get_label('save_messages_secretly', 'Save messages secretly') ?></span>
        </td>
    </tr>
</table>
<?php endif; ?>

<?php if($get == 'users' && !!$lastMessage): ?>
<?php
$lastMessageBody = mb_convert_encoding($lastMessage->body, 'UTF-8', 'UTF-8');
$lastMessageBody = strlen($lastMessageBody) > 30 ? mb_substr($lastMessageBody, 0, 30, 'UTF-8') . '..' : $lastMessageBody;
?>
<table class="messenger-list-item" data-contact="<?php echo e($user->id); ?>">
    <tr data-action="0">
        
        <td style="position: relative">
            <?php if($user->active_status): ?>
            <span class="activeStatus"></span>
            <?php endif; ?>
            <div class="avatar av-m" style="background-image: url('<?php echo e($user->avatar); ?>');">
            </div>
        </td>
        
        <td>
            <p data-id="<?php echo e($user->id); ?>" data-type="user">
                <!-- <?php echo e(strlen($user->first_name.' '.$user->last_name) > 12 ? trim(substr($user->first_name.' '.$user->last_name,0,12)).'..' : $user->first_name.' '.$user->last_name); ?> -->
                <?php echo e($user->first_name.' '.$user->last_name); ?>

                <span class="contact-item-time" data-time="<?php echo e($lastMessage->created_at); ?>"><?php echo e($lastMessage->timeAgo); ?></span>
            </p>
            <span>
                
                <?php echo $lastMessage->from_id == Auth::user()->id
                ? '<span class="lastMessageIndicator">You :</span>'
                : ''; ?>

                
                <?php if($lastMessage->attachment == null): ?>
                <?php echo $lastMessageBody; ?>

                <?php else: ?>
                <span class="fas fa-file"></span> <?= get_label('attachment', 'Attachment') ?>
                <?php endif; ?>
            </span>
            
            <?php echo $unseenCounter > 0 ? "<b>".$unseenCounter."</b>" : ''; ?>

        </td>
    </tr>
</table>
<?php endif; ?>

<?php if($get == 'search_item'): ?>
<table class="messenger-list-item" data-contact="<?php echo e($user->id); ?>">
    <tr data-action="0">
        
        <td>
            <div class="avatar av-m" style="background-image: url('<?php echo e($user->avatar); ?>');">
            </div>
        </td>
        
        <td>
            <p data-id="<?php echo e($user->id); ?>" data-type="user">
                <!-- <?php echo e(strlen($user->first_name.' '.$user->last_name) > 12 ? trim(substr($user->first_name.' '.$user->last_name,0,12)).'..' : $user->first_name.' '.$user->last_name); ?> -->
                <?php echo e($user->first_name.' '.$user->last_name); ?>

        </td>
    </tr>
</table>
<?php endif; ?>

<?php if($get == 'sharedPhoto'): ?>
<div class="shared-photo chat-image" style="background-image: url('<?php echo e($image); ?>')"></div>
<?php endif; ?>
<?php /**PATH C:\Siddharth Gor\Test\resources\views/vendor/Chatify/layouts/listItem.blade.php ENDPATH**/ ?>