<?php
/**
 * Created by JetBrains PhpStorm.
 * User: DATTWOOD
 * Date: 09/12/11
 * Time: 11:20
 * Generate Pie charts with posted data
 */

require_once('jpgraph/src/jpgraph.php');
require_once ('jpgraph/src/jpgraph_pie.php');
require_once ('jpgraph/src/jpgraph_pie3d.php');

$graph = new PieGraph(297, 297);
$graph->title->Set($_GET['var2']);
$graph->title->SetFont(FF_ARIAL, FS_NORMAL, 14);

$graph->SetFrame(false);
$graph->legend->SetPos(0.5, 0.99, 'center', 'bottom');
$graph->legend->SetColumns(3);
$graph->SetAntiAliasing();
//$graph->SetMargin(40, 40, 20, 100);
$p1 = new PiePlot3D(unserialize(urldecode($_GET['var1'])));
$p1->SetLegends(unserialize(urldecode($_GET['var3'])));
$p1->value->Show(false);

$graph->legend->SetFont(FF_ARIAL, FS_NORMAL, 8);
$graph->Add($p1);
$p1->SetSliceColors(unserialize(urldecode($_GET['var4'])));
$graph->Stroke();

?>