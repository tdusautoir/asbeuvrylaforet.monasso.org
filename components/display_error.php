<?php if (isset_flash_message_by_type(FLASH_SUCCESS)) : ?>
    <div class="success"><?php display_flash_message_by_type(FLASH_SUCCESS); ?></div>
<?php elseif (isset_flash_message_by_type(FLASH_ERROR)) : ?>
    <div class="error"><?php display_flash_message_by_type(FLASH_ERROR); ?></div>
<?php endif; ?>