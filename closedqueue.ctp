<?php echo $this->Html->script('http://maps.google.com/maps/api/js?sensor=true', false); ?>
<div class="facilities index">
	<h2><?php echo __('Facilities'); ?></h2>
	<h3><?php echo __('重力モデルによる推移確率'); ?></h2>
	<table cellpadding="0" cellspacing="0">
        <thead>
	<?php
		for($i = 0;$i < count($gravity[0]); $i++){
			echo "<tr>";
			for($j = 0; $j < count($gravity[0]); $j++){
				echo "<td>";
				echo sprintf("%.3f",$gravity[$i][$j]);
				echo "</td>";
			} 
        		echo "</tr>";
		}
	?>
	</thead>
	</table>

	<h3><?php echo __('トラフィック方程式の行列'); ?></h2>
        <table cellpadding="0" cellspacing="0">
        <thead>         
        <?php   
                for($i = 0;$i < count($ff[0]); $i++){
                        echo "<tr>";
                        for($j = 0; $j < count($ff[0]); $j++){
                                echo "<td>";
                                echo sprintf("%.3f",$ff[$i][$j]);
                                echo "</td>";
                        }
                        echo "</tr>";
                }       
        ?>      
        </thead>
        </table>

	<h3><?php echo __('トラフィック方程式の右辺'); ?></h2>
        <table cellpadding="0" cellspacing="0">
        <thead>         
	<tr>
        <?php   
                for($i = 0;$i < count($bb); $i++){
                	echo "<td>";
                        echo sprintf("%.3f",$bb[$i]);
                        echo "</td>";
                }       
        ?>      
	</tr>
        </thead>
        </table>

	<h3><?php echo __('トラフィック方程式の解α'); ?></h2>
        <table cellpadding="0" cellspacing="0">
        <thead>         
        <tr>    
        <?php   
                for($i = 0;$i < count($alpha); $i++){
                        echo "<td>";
                        echo sprintf("%.3f",$alpha[$i]);
                        echo "</td>";
                }       
        ?>              
        </tr>   
        </thead>
        </table>

	<h3><?php echo __('平均系内人数L'); ?></h2>
        <table cellpadding="0" cellspacing="0">
        <thead>         
        <tr>    
        <?php   
                for($i = 0;$i < count($L); $i++){
                        echo "<td>";
                        echo sprintf("%.3f",$L[$i]);
                        echo "</td>";
                }
        ?>              
        </tr>   
        </thead>
        </table>

	<h3><?php echo __('平均ノード滞在時間R'); ?></h2>
        <table cellpadding="0" cellspacing="0">
        <thead>         
        <tr>    
        <?php
                for($i = 0;$i < count($R); $i++){
                        echo "<td>";
                        echo sprintf("%.3f",$R[$i]);
                        echo "</td>";
                }
        ?>              
        </tr>   
        </thead>
        </table>

	<h3><?php echo __('スループット'); ?></h2>
        <table cellpadding="0" cellspacing="0">
        <thead>         
        <tr>    
        <?php
                for($i = 0;$i < count($lambda); $i++){
                        echo "<td>";
                        echo sprintf("%.3f",$lambda[$i]);
                        echo "</td>";
                }
        ?>              
        </tr>   
        </thead>
        </table>

</div>

