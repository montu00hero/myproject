<?php

/*
  
CREATE TABLE IF NOT EXISTS `testing` (
  `topic` varchar(100) NOT NULL,
  `class` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `testing`
--

INSERT INTO `testing` (`topic`, `class`) VALUES
('t1', '1,2,3,4,6'),
('t2', '2,5,6,7'),
('t3', '1,5,6'),
('t4', '4,5,6,7'),
('t5', '2,9,6,8'),
('t6', '2,9,1,6,8');

 
 *  */





$host='localhost';
$user='root';
$pwd='';

$conn=mysql_connect($host,$user,$pwd);

$db=mysql_select_db('test');

echo"<table border='1'><tr><th>Class</<th><th>Topic</th><th>Count</th></tr>";

for($i=1;$i<10;$i++)
 {
   
   echo"<pre>", $query='select Group_concat(topic) as topic ,count(class) as counts from testing where find_in_set('.$i.',class) ';
   $qu=mysql_query($query);
    
    while ($row = mysql_fetch_array($qu)) {

        echo'<tr><td>'.$i.'</td><td>'.($row['topic']).'</td><td>'.$row['counts'].'</td></tr>';

    }
    
   
 }
echo'</table>';
//print_r($conn);


mysql_close();



