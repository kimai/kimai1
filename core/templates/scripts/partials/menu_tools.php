
<div id="menu">
    <?php echo $this->links('logout'); ?>
    <a id="main_tools_button" href="#" ><img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/g3_menu_dropdown.png" width="44" height="27" alt="Menu Dropdown" /></a>
    <br/><?php echo $this->kga['lang']['logged_in_as']?> <b><?php echo $this->username(); ?></b>
</div>

<div id="main_tools_menu">
    <div class="slider">
        <?php echo $this->links('credits'); ?> |
        <?php echo $this->links('preferences'); ?>
    </div>
    <div class="end"></div>
</div>