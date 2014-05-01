<a onClick="floaterShow('floaters.php','add_edit_project',0,0,650); $(this).blur(); return false;"
   href="#" ><?php echo $this->icons('add', array('title' => $this->kga['lang']['new_project'])); ?></a>
<?php echo $this->kga['lang']['new_project']?>
<br/><br/>

<table>

<thead>
  <tr class="headerrow">
      <th class="admin_options"><?php echo $this->kga['lang']['options']?></th>
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
                    <a href ="#" onClick="editSubject('project',<?php echo $row['projectID']?>); $(this).blur(); return false;">
                        <?php echo $this->icons('edit'); ?></a>
                    &nbsp;
                    <a href="#" id="delete_project<?php echo $row['projectID']?>" onClick="adminPanel_extension_deleteProject(<?php echo $row['projectID']?>)">
                        <?php echo $this->icons('delete', array('title' => $this->kga['lang']['delete_project'])); ?></a>
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