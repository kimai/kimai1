<?php
$adminMenus = array(
    array(
        'title' => $this->kga['lang']['customers'],
        'class' => 'adminPanel_extension_subject',
        'content' => $this->customer_display,
    ), array(
        'title' => $this->kga['lang']['projects'],
        'class' => 'adminPanel_extension_subject',
        'content' => $this->project_display,
    ),
    array(
        'title' => $this->kga['lang']['activities'],
        'class' => 'adminPanel_extension_subject',
        'content' => $this->activity_display,
    ),
    array(
        'title' => $this->kga['lang']['users'],
        'class' => 'adminPanel_extension_4cols',
        'content' => $this->admin['users'],
    ), array(
        'title' => $this->kga['lang']['groups'],
        'class' => 'adminPanel_extension_4cols',
        'content' => $this->admin['groups'],
    ), array(
        'title' => $this->kga['lang']['globalRoles'],
        'class' => '',
        'content' => $this->globalRoles_display,
    ), array(
        'title' => $this->kga['lang']['membershipRoles'],
        'class' => '',
        'content' => $this->membershipRoles_display,
    ), array(
        'title' => $this->kga['lang']['status'],
        'class' => 'adminPanel_extension_4cols',
        'content' => $this->admin['status'],
    )
);

if ($this->showAdvancedTab) {
    $adminMenus[] = array(
        'title' => $this->kga['lang']['advanced'],
        'class' => 'adminPanel_extension_4cols',
        'content' => $this->admin['advanced'],
    );
}

if (isset($this->admin['database'])) {
    $adminMenus[] = array(
        'title' => $this->kga['lang']['database'],
        'class' => 'adminPanel_extension_4cols',
        'content' => $this->admin['database'],
    );
}
?>
    <script type="text/javascript">
        $(document).ready(function() {
            adminPanel_extension_onload();
        }); 
    </script>

<div id="adminPanel_extension_panel">
<?php
    echo $this->accordion()->start();

    foreach($adminMenus as $menuEntry)
    {
        echo $this->accordion()->renderEntry($menuEntry['title'], $menuEntry['content'], $menuEntry);
    }

    echo $this->accordion()->end();
?>
</div>
