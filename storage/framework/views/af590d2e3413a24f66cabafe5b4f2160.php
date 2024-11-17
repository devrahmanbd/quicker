
<div class="avatar av-l chatify-d-flex"></div>
<p class="info-name"><?php echo e(Auth::user()->first_name.' '.Auth::user()->last_name); ?></p>
<div class="messenger-infoView-btns">
    <a href="javascript:void(0);" class="danger delete-conversation"><?= get_label('delete_conversation', 'Delete Conversation') ?></a>
</div>

<div class="messenger-infoView-shared">
    <p class="messenger-title"><span><?= get_label('shared_photos', 'Shared Photos') ?></span></p>
    <div class="shared-photos-list"></div>
</div><?php /**PATH C:\Siddharth Gor\Test\resources\views/vendor/Chatify/layouts/info.blade.php ENDPATH**/ ?>