<?php
/**
 * Created by JetBrains PhpStorm.
 * User: DATTWOOD
 * Date: 09/12/11
 * Time: 11:20
 * Generate Pie charts with posted data
 */

require_once('jpgraph/src/jpgraph.php');
require_once ('jpgraph/src/jpgraph_bar.php');


//$graph = new BarPlot($_GET['var5'], $_GET['var5']);
//$graph->title->Set($_GET['var2']);
//$graph->title->SetFont(FF_ARIAL, FS_NORMAL, 14);
//
//$graph->SetFrame(false);
//$graph->legend->SetPos(0.5, 0.99, 'center', 'bottom');
//$graph->legend->SetColumns(3);
//$graph->SetAntiAliasing();
////$graph->SetMargin(40, 40, 20, 100);
//$p1 = new BarPlot(unserialize(urldecode($_GET['var1'])));
//$p1->SetLegends(unserialize(urldecode($_GET['var3'])));
//$p1->value->Show(false);
//
//$graph->legend->SetFont(FF_ARIAL, FS_NORMAL, 8);
//$graph->Add($p1);
//$p1->SetSliceColors(unserialize(urldecode($_GET['var4'])));
//$graph->Stroke();


// Create the graph. These two calls are always required
$graph = new Graph(300,200);
$graph->SetScale('intlin');

// Add a drop shadow
$graph->SetShadow();

// Adjust the margin a bit to make more room for titles
$graph->SetMargin(40,30,20,40);

// Create a bar pot
$bplot = new BarPlot(unserialize(urldecode($_GET['var1'])));

// Adjust fill color
$bplot->SetFillColor('orange');
$graph->Add($bplot);

// Setup the titles
$graph->title->Set($_GET['var2']);
$graph->xaxis->title->Set('X-title');
$graph->yaxis->title->Set('Y-title');

// Setup labels
$lbl = array("Andrew\nTait","Thomas\nAnderssen","Kevin\nSpacey","Nick\nDavidsson",
"David\nLindquist","Jason\nTait","Lorin\nPersson");
$graph->xaxis->SetTickLabels(unserialize(urldecode($_GET['var3'])));

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

// Display the graph
$graph->Stroke();


?>