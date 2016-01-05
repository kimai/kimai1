<a href="#" onclick="floaterShow('floaters.php','add_edit_project',0,0,650); $(this).blur(); return false;"><img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/add.png" width="22" height="16" alt="<?php echo $this->kga['lang']['new_project']?>"></a> <?php echo $this->kga['lang']['new_project']?>
<br/><br/>

<table>

<thead>
  <tr class="headerrow">
      <th><?php echo $this->kga['lang']['options']?></th>
      <th><?php echo $this->kga['lang']['projects']?></th>
      <th><?php echo $this->kga['lang']['groups']?></th>
  </tr>
</thead>

<tbody>
<?php
    if (!isset($this->projects) || $this->projects == '0' || count($this->projects) == 0)
    {
        ?>
        <tr>
            <td nowrap colspan='3'>
                <?php echo $this->error(); ?>
            </td>
        </tr>
        <?php
    }
    else
    {
        foreach ($this->projects as $row)
        {
            ?>
            <tr class="<?php echo $this->cycle(array("odd","even"))->next()?>">

                <td class="option">
                    <a href ="#" onclick="editSubject('project',<?php echo $row['projectID']?>); $(this).blur(); return false;"><img
                            src='../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/edit2.gif' width='13' height='13'
                            alt='<?php echo $this->kga['lang']['edit']?>' title='<?php echo $this->kga['lang']['edit']?>' border='0' /></a>
                    &nbsp;
                    <a href="#" id="delete_project<?php echo $row['projectID']?>" onclick="adminPanel_extension_deleteProject(<?php echo $row['projectID']?>)"><img
                            src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/button_trashcan.png" title="<?php echo $this->kga['lang']['delete_project']?>"
                            width="13" height="13" alt="<?php echo $this->kga['lang']['delete_project']?>" border="0"></a>
                </td>

                <td class="projects">
                    <?php if ($row['visible'] != 1):?><span style="color:#bbb"><?php endif; ?>
                    <?php if ($this->kga['conf']['flip_project_display']): ?>
                    <span class="lighter"><?php echo $this->escape($this->truncate($row['customerName'],30,"..."))?>:</span> <?php echo $this->escape($row['name']) ?>
                    <?php else: ?>
                    <?php echo $this->escape($row['name'])?> <span class="lighter">(<?php echo $this->escape($this->truncate($row['customerName'],30,"..."))?>)</span>
                    <?php endif; ?>
                    <?php if ($row['visible'] != 1):?></span><?php endif; ?>
                </td>

                <td>
                    <?php echo $this->escape($row['groups'])?>
                </td>

            </tr>
        <?php
        }
    }
    ?>
    </tbody>
</table>