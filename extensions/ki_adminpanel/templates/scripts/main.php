<div id="adminPanel_extension_panel">

    <?php if (isset($this->tab_customer)): ?>
        <?php echo $this->adminScreen()->accordion(6, $this->translate('customers'), $this->tab_customer); ?>
    <?php endif; ?>

    <?php if (isset($this->tab_project)): ?>
        <?php echo $this->adminScreen()->accordion(7, $this->translate('projects'), $this->tab_project); ?>
    <?php endif; ?>

    <?php if (isset($this->tab_activity)): ?>
        <?php echo $this->adminScreen()->accordion(8, $this->translate('activities'), $this->tab_activity); ?>
    <?php endif; ?>

    <?php if (isset($this->tab_users)): ?>
        <?php echo $this->adminScreen()->accordion(1, $this->translate('users'), $this->tab_users); ?>
    <?php endif; ?>

    <?php if (isset($this->tab_groups)): ?>
        <?php echo $this->adminScreen()->accordion(2, $this->translate('groups'), $this->tab_groups); ?>
    <?php endif; ?>

    <?php if (isset($this->tab_globalrole)): ?>
        <?php echo $this->adminScreen()->accordion(9, $this->translate('globalRoles'), $this->tab_globalrole); ?>
    <?php endif; ?>

    <?php if (isset($this->tab_membershiprole)): ?>
        <?php echo $this->adminScreen()->accordion(10, $this->translate('membershipRoles'), $this->tab_membershiprole); ?>
    <?php endif; ?>

    <?php if (isset($this->tab_status)): ?>
        <?php echo $this->adminScreen()->accordion(3, $this->translate('status'), $this->tab_status); ?>
    <?php endif; ?>

    <?php if (isset($this->tab_advanced)): ?>
        <?php echo $this->adminScreen()->accordion(4, $this->translate('advanced'), $this->tab_advanced); ?>
    <?php endif; ?>

    <?php if (isset($this->tab_database)): ?>
        <?php echo $this->adminScreen()->accordion(5, $this->translate('database'), $this->tab_database); ?>
    <?php endif; ?>

</div>

<script type="text/javascript">
    $(document).ready(function () {
        adminPanel_extension_onload();
    });
</script>