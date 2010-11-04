<?
class Calendar
{
	var $year;
	var $month;
	var $numdays;

	//Constructor
	function Calendar($m=false, $y=false) {
		$this->setYear($y);
		$this->setMonth($m);
	}

	//Mes
   function setMonth($m=false) {
		if (!$m) {
			$this->month = date("m");
		} else {
			$this->month = $m;
		}
		$this->numdays = $this->getDaysInMonth();
		return true;
	}
	//Anho
	function setYear($y=false) {
		if (!$y) {
			$this->year = date("Y");
		} else {
			$this->year = $y;
		}
		return true;
	}
	//Total Días del mes
	function getDaysInMonth() {
		return date("t", mktime(0, 0, 0, $this->month,01,$this->year));
	}

	//Devuelve mes
	function getMonth($format=false) {
		if (!$format) {
			return mktime(0, 0, 0, $this->month,01,$this->year);
		} else {
			return date($format, mktime(0, 0, 0, $this->month,01,$this->year));
		}
	}

	//Siguiente mes
	function nextMonth($format=false) {
		if (!$format) {
			return mktime(0, 0, 0, $this->month+1,01,$this->year);
		} else {
			return date($format, mktime(0, 0, 0, $this->month+1,01,$this->year));
		}
	}
	//Mes anterior
	function lastMonth($format=false) {
		if (!$format) {
			return mktime(0, 0, 0, $this->month,0,$this->year);
		} else {
			return date($format, mktime(0, 0, 0, $this->month,0,$this->year));
		}
	}
	//Calendario Mes completo
	function calendarMonth($format=false) {
		for ($i=1; $i<=$this->numdays; $i++) {
			$wday = date("D", mktime(0, 0, 0, $this->month, $i, $this->year)); // textual day of week e.g. Sun
			$week = date("W", mktime(0, 0, 0, $this->month, $i, $this->year)); // week of year
			if (!$format) {
				$month[$week][$wday] = mktime(0, 0, 0, $this->month, $i, $this->year);
			} else {
				$month[$week][$wday] = date($format, mktime(0, 0, 0, $this->month, $i, $this->year));
			}
		}
		return $month;
	}
//Dibuja calendario por semanas. Por ahora con el contenido antiguo hasta cambios
//$di, días que tienen contenido

	function drawCalendar($tstamp,$di,$parameters=NULL)
	{
		$thisMonth =  date('m', $tstamp);
		$thisYear =  date('Y', $tstamp);

		//1-OCT-2007 Semana 0, BD table contents->archive 1
		$sem1=mktime(1,1,1,10,1,2007);
		$primmes=mktime(1,1,1,$thisMonth,1,$thisYear);
		$semmes=number_format((($primmes-$sem1)/(60*60*24*7)),0);
		$numsem=$semmes;
		//desde semana1 hasta

		$nu=date('t',mktime(0,0,0,$thisMonth,1,$thisYear));
		$total = count($di); //total dias con contenido
		$days_content=array();
	    for($i=1;$i<=$nu;$i++){
	      	$days_content[$i]=0;
	        for($j=0;$j<=$total;$j++)
	        {
	        	if($di[$j]==$i)
	        	 {$days_content[$i]=1;}

	       	 }
	     }
		$hoy=mktime(1,1,1,date(m),date(d),date(Y));
		$semhoy=number_format((($hoy-$sem1)/(60*60*24*7)),0);
		$diaHoy=date("j",$hoy);
	$calendario="";
		$wday_index = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
		$days_name = array('Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom');
		$month_name = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

		$calendario=$calendario. '<table summary="calendario" cellspacing="0" border="0" align="center" width="99%">
				<caption class="calTitle"><b>';
		$calendario=$calendario.$month_name[date("n", mktime(0,0,0,$this->month, 1, $this->year))]." - ".date("Y", mktime(0,0,0,$this->month, 1, $this->year)).'</b></caption>';
		$calendario=$calendario.'<tr> <th class="calDayName">'.implode("</th><th class=\"calDayName\">", $days_name).'</th></tr>';

		foreach ($this->calendarMonth('j') as $stamp) {
			$calendario=$calendario.'<tr>';
			for ($i=0; $i < count($wday_index); $i++) {
	 			$theDay = $stamp[$wday_index[$i]]; //Dia de la semana
				$calendario=$calendario.'<td class="calDay" width="14%">';
				if ($theDay > 0){
					if($wday_index[$i]=='Mon'){ $numsem=($semmes+number_format(($theDay/7),0)+1);}
						if($numsem >= 0) {
							if($numsem >= $semhoy)
							{
									if(($theDay <= $diaHoy) OR ($numsem==$semhoy))
									{
										$calendario=$calendario.'<a href="index.php" class="Day">'.$theDay.'</a>';

									}else{
										$calendario=$calendario.$theDay;
									}
							}else{
									$calendario=$calendario.'<a href="hemeroteca.php?stamp='.$tstamp.'&se='.$numsem.'&d='.$theDay.'" class="Day">'.$theDay.'</a>';
							}
						}else
						{	$calendario=$calendario.$theDay;}
				}
				$calendario=$calendario.'</td>';
			}
		    $calendario=$calendario.'</tr>';
		}
		$calendario=$calendario.'<tr><td colspan="4" class="calTitle" align="left">';
							$calendario=$calendario.'<a class="calNav" href="'.$_SERVER['PHP_SELF'].'?stamp='.$this->lastMonth().'">&laquo; '.$month_name[date("n",$this->lastMonth())].'</a></td>';
							$calendario=$calendario.'<td class="calTitle" colspan="4" align="right">';
							if(($tstamp) && ($this->nextMonth() <= $hoy)) {
									$calendario=$calendario.'<a class="calNav" href="'.$_SERVER['PHP_SELF'].'?stamp='.$this->nextMonth().'">'.$month_name[date("n",$this->nextMonth())].'  &raquo;</a>';
								 }
		$calendario=$calendario.'</td></tr>
				</table>';

		return $calendario;
	}

/* Por días
 function drawCalendar($tstamp,$di,$parameters=NULL)
	{
		$thisMonth =  date('m', $tstamp);
		$thisYear =  date('Y', $tstamp);

		$nu=date('t',mktime(0,0,0,$thisMonth,1,$thisYear));
		$total = count($di); //total dias con contenido
		$days_content=array();
	    for($i=1;$i<=$nu;$i++){
	      	$days_content[$i]=0;
	        for($j=0;$j<=$total;$j++)
	        {
	        	if($di[$j]==$i)
	        	 {$days_content[$i]=1;}

	       	 }
	     }
		$hoy=mktime(0,0,0,date(m),date(d),date(Y));

		$wday_index = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
		$days_name = array('Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom');
		$month_name = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

		$calendario= '<table summary="calendario" cellspacing="0" border="0" align="center" width="99%">
				<caption class="calTitle"><b>';
		$calendario=$calendario.$month_name[date("n", mktime(0,0,0,$this->month, 1, $this->year))]." - ".date("Y", mktime(0,0,0,$this->month, 1, $this->year)).'</b></caption>';
		$calendario=$calendario.'<tr> <th class="calDayName">'.implode("</th><th class=\"calDayName\">", $days_name).'</th></tr>';

		foreach ($this->calendarMonth('j') as $stamp) {
			$calendario=$calendario.'<tr>';
			for ($i=0; $i < count($wday_index); $i++) {
	 			$theDay = $stamp[$wday_index[$i]];
				$calendario=$calendario.'<td class="calDay" width="14%">';
				if ($theDay > 0){
					if($days_content[$theDay] > 0) {
						$calendario=$calendario.'<a href="hemeroteca.php?stamp='.$tstamp.'&d='.$theDay.'" class="Day">'.$theDay.'</a>';
						// comprobar si hoy enlace a portada
					}else
					{	$calendario=$calendario.$theDay;}
				}
				$calendario=$calendario.'</td>';
			}
		    $calendario=$calendario.'</tr>';
		}
		$calendario=$calendario.'<tr><td colspan="4" class="calTitle" align="left">';
							$calendario=$calendario.'<a class="calNav" href="'.$_SERVER['PHP_SELF'].'?stamp='.$this->lastMonth().'">&laquo; '.$month_name[date("n",$this->lastMonth())].'</a></td>';
							$calendario=$calendario.'<td class="calTitle" colspan="4" align="right">';
							if(($tstamp) && ($this->nextMonth() < $hoy)) {
									$calendario=$calendario.'<a class="calNav" href="'.$_SERVER['PHP_SELF'].'?stamp='.$this->nextMonth().'">'.$month_name[date("n",$this->nextMonth())].'  &raquo;</a>';
								 }
		$calendario=$calendario.'</td></tr>
				</table>';

		return $calendario;
	}


 */

}
?>