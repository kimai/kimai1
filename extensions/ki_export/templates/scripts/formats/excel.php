<html xmlns:o="urn:schemas-microsoft-com:office:office" 
xmlns:x="urn:schemas-microsoft-com:office:excel" 
xmlns="http://www.w3.org/TR/REC-html40"> 

<head> 
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<style> 
.date { 
mso-number-format:"Short Date"; 
} 
.time { 
mso-number-format:"h\:mm\:ss\;\@"; 
} 
.duration {
mso-number-format:"h\:mm\;\@";
}
.decimal {
mso-number-format:Fixed;
}
</style> 
<!--[if gte mso 9]><xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>Tabelle1</x:Name>
    <x:WorksheetOptions>
     <x:DefaultColWidth>10</x:DefaultColWidth>
     <x:Selected/>
     <x:Panes>
      <x:Pane>
       <x:Number>3</x:Number>
       <x:ActiveRow>4</x:ActiveRow>
       <x:ActiveCol>3</x:ActiveCol>
      </x:Pane>
     </x:Panes>
     <x:ProtectContents>False</x:ProtectContents>
     <x:ProtectObjects>False</x:ProtectObjects>
     <x:ProtectScenarios>False</x:ProtectScenarios>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml><![endif]-->
</head> 

<body> 
<table> 
<thead><tr> 
<!-- column headers -->
<?php if (isset($this->columns['date'])):         ?> <td><?php echo $this->kga['lang']['datum']?></td>       <?php endif; ?>
<?php if (isset($this->columns['from'])):         ?> <td><?php echo $this->kga['lang']['in']?></td>          <?php endif; ?>
<?php if (isset($this->columns['to'])):           ?> <td><?php echo $this->kga['lang']['out']?></td>         <?php endif; ?>
<?php if (isset($this->columns['time'])):         ?> <td><?php echo $this->kga['lang']['time']?></td>        <?php endif; ?>
<?php if (isset($this->columns['dec_time'])):     ?> <td><?php echo $this->kga['lang']['timelabel']?></td>   <?php endif; ?>
<?php if (isset($this->columns['rate'])):         ?> <td><?php echo $this->kga['lang']['rate']?></td>        <?php endif; ?>
<?php if (isset($this->columns['wage'])):         ?> <td><?php echo $this->kga['currency_name']?>}</td>      <?php endif; ?>
<?php if (isset($this->columns['budget'])):       ?> <td><?php echo $this->kga['lang']['budget']?></td>      <?php endif; ?>
<?php if (isset($this->columns['approved'])):     ?> <td><?php echo $this->kga['lang']['approved']?></td>    <?php endif; ?>
<?php if (isset($this->columns['status'])):       ?> <td><?php echo $this->kga['lang']['status']?></td>      <?php endif; ?>
<?php if (isset($this->columns['billable'])):     ?> <td><?php echo $this->kga['lang']['billable']?></td>    <?php endif; ?>
<?php if (isset($this->columns['customer'])):     ?> <td><?php echo $this->kga['lang']['customer']?></td>    <?php endif; ?>
<?php if (isset($this->columns['project'])):      ?> <td><?php echo $this->kga['lang']['project']?></td>     <?php endif; ?>
<?php if (isset($this->columns['activity'])):     ?> <td><?php echo $this->kga['lang']['activity']?></td>    <?php endif; ?>
<?php if (isset($this->columns['description'])):  ?> <td><?php echo $this->kga['lang']['description']?></td> <?php endif; ?>
<?php if (isset($this->columns['comment'])):      ?> <td><?php echo $this->kga['lang']['comment']?></td>     <?php endif; ?>
<?php if (isset($this->columns['location'])):     ?> <td><?php echo $this->kga['lang']['location']?></td>   <?php endif; ?>
<?php if (isset($this->columns['trackingNumber'])):   ?> <td><?php echo $this->kga['lang']['trackingNumber']?></td>  <?php endif; ?>
<?php if (isset($this->columns['user'])):         ?> <td><?php echo $this->kga['lang']['username']?></td>    <?php endif; ?>
<?php if (isset($this->columns['cleared'])):      ?> <td><?php echo $this->kga['lang']['cleared']?></td>     <?php endif; ?>
</tr> 
</thead> 
<?php foreach($this->exportData as $row): ?>
<tr> 

<?php if (isset($this->columns['date'])): ?>
                    <td class=date>
                        <?php  if ($this->custom_dateformat)
                            echo $this->escape(strftime($this->custom_dateformat,$row['time_in']));
                          else
                            echo $this->escape(strftime($this->kga['date_format'][1], $row['time_in']));
                        ?>
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['from'])): ?>
                    <td align=right class=time>
                        <?php  if ($this->custom_timeformat)
                            echo $this->escape(strftime($this->custom_timeformat,$row['time_in']));
                          else
                            echo $this->escape(strftime("%H:%M", $row['time_in']));
                        ?>
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['to'])): ?>
                    <td align=right class=time>
                    
<?php if ($row['time_out']): ?>
                        <?php  if ($this->custom_timeformat)
                            echo $this->escape(strftime($this->custom_timeformat,$row['time_out']));
                          else
                            echo $this->escape(strftime("%H:%M", $row['time_in']));
                        ?>
<?php else: ?>      
                        &ndash;&ndash;:&ndash;&ndash;
<?php endif; ?>
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['time'])): ?>
                    <td align=right class=duration>
                        <?php echo $row['duration'] ? $row['formattedDuration'] : "&ndash;:&ndash;&ndash;" ?>
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['dec_time'])): ?>
                    <td align=right class=decimal>
                        <?php echo $row['decimalDuration'] ?$row['decimalDuration'] : "&ndash;:&ndash;&ndash;" ?>
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['rate'])): ?>
                    <td align=right class=decimal>
                            <?php echo $row['rate'] ?>
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['wage'])): ?>
                    <td align=right class=decimal>
                        <?php echo $row['wage']? $row['wage'] : "&ndash;" ?>
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['budget'])): ?>
                    <td>
                        <?php echo $this->escape($row['budget']); ?>
                    </td>
<?php endif; ?>



<?php if (isset($this->columns['approved'])): ?>
                    <td>
                        <?php echo $this->escape($row['approved']); ?>
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['status'])): ?>
                    <td>
                        <?php echo $this->escape($row['status']); ?>
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['billable'])): ?>
                    <td>
                        <?php echo $this->escape($row['billable']); ?>%
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['customer'])): ?>
                    <td>
                        <?php echo $this->escape($row['customerName']); ?>
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['project'])): ?>
                    <td>
                            <?php echo $this->escape($row['projectName']); ?>
                    </td>
<?php endif; ?>



<?php if (isset($this->columns['activity'])): ?>
                    <td>
                            <?php echo $this->escape($row['activityName']); ?> 
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['description'])): ?>
                    <td>
                        <?php echo $this->escape($row['description']); ?>%
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['comment'])): ?>
                    <td>
                        <?php echo str_replace("\n", "&#10;", $this->escape($row['comment'])); ?>
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['location'])): ?>
                    <td>
                        <?php echo $this->escape($row['location']); ?>
                        
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['trackingNumber'])): ?>
                    <td>
                        <?php echo $this->escape($row['trackingNumber']); ?>
                        
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['user'])): ?>
                    <td>
                        <?php echo $this->escape($row['username']); ?>
                        
                    </td>
<?php endif; ?>


<?php if (isset($this->columns['cleared'])): ?>
          <td>
                      <?php if ($row['cleared']) echo "cleared" ?>
          </td>
<?php endif; ?>
          

                </tr>
               
<?php endforeach; ?>

</table> 

</body> 
</html>  
 
