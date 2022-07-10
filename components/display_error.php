<?php if (isset_flash_message_by_type(FLASH_SUCCESS)) : ?>
    <div class="success" id="flash"><?php display_flash_message_by_type(FLASH_SUCCESS); ?></div>
    <script>
        flash = document.getElementById("flash");
        if (flash) {
            setTimeout(function() {
                flash.style.transform = "translateY(-140px)"
                setTimeout(function() {
                    flash.style.display = "none"
                }, 400)
            }, 2000)
        }
    </script>
<?php elseif (isset_flash_message_by_type(FLASH_ERROR)) : ?>
    <div class="error" id="flash"><?php display_flash_message_by_type(FLASH_ERROR); ?></div>
    <script>
        flash = document.getElementById("flash");
        if (flash) {
            setTimeout(function() {
                flash.style.transform = "translateY(-140px)"
                setTimeout(function() {
                    flash.style.display = "none"
                }, 400)
            }, 3000)
        }
    </script>
<?php endif; ?>