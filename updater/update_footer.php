<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team since 2006
 *
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 *
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('KIMAI_UPDATER_RUNNING')) {
    die('You cannot call this file directly');
}


if ((int)$revisionDB == $kga['revision'])
{
    echo '<script type="text/javascript">window.location.href = "../index.php";</script>';
}
else
{
    $l2 = $kga['lang']['login'];
    $l3 = $kga['lang']['updater'][90];

    if (!$errors)
    {
        $l1 = $kga['lang']['updater'][80];

        echo <<<EOD
<script type="text/javascript">
$("#link").append("<p><strong>$l1</strong></p>");
$("#link").append("<h1><a href='../index.php'>$l2</a></h1>");
$("#link").addClass("success");
$("#queries").append("$executed_queries $l3</p>");
</script>
EOD;
    }
    else
    {
        $l1 = $kga['lang']['updater'][100];

        echo <<<EOD
<script type="text/javascript">
$("#link").append("<p><strong>$l1</strong></p>");
$("#link").append("<h1><a href='../index.php'>$l2</a></h1>");
$("#link").addClass("fail");
$("#queries").append("$executed_queries $l3");
</script>
EOD;
    }
}

?>
</table>
<?php
if (isset($new_passwords))
{
    ?>
    <br/><br/>
    <script type="text/javascript">
        $("#important_message").append("<?php echo $kga['lang']['updater'][120] . ' <br/>'; ?>");
        $("#important_message").show();
    </script>
    <div class="important_block_head"> <?php echo $kga['lang']['updater'][110]; ?>:</div>
    <table style="width:100%">
        <tr>
            <td><i> <?php echo $kga['lang']['username']; ?> </i></td>
            <td><i> <?php echo $kga['lang']['password']; ?> </i></td>
        </tr>
        <?php
        foreach ($new_passwords as $username => $password) {
            echo "<tr><td>$username</td><td>$password</td></tr>";
        }
        ?>
    </table><br/>
    <?php
}
?>
<?php echo "$executed_queries " . $kga['lang']['updater'][90]; ?>
<h1><a href='../index.php'><?php echo $kga['lang']['login']; ?></a></h1>
</body>
</html>
