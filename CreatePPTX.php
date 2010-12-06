<?php
/**
 * PHPPowerPoint
 *
 * Copyright (c) 2009 - 2010 PHPPowerPoint
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPPowerPoint
 * @package    PHPPowerPoint
 * @copyright  Copyright (c) 2009 - 2010 PHPPowerPoint (http://www.codeplex.com/PHPPowerPoint)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Error reporting */
error_reporting(E_ALL);

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . 'Lib');

/** PHPPowerPoint */
include 'PHPPowerPoint.php';
include 'Data.php';


// Create new PHPPowerPoint object
echo date('H:i:s') . " Create new PHPPowerPoint object\n";
$objPHPPowerPoint = new PHPPowerPoint();

// Set properties
echo date('H:i:s') . " Set properties\n";
$objPHPPowerPoint->getProperties()->setCreator("Maarten Balliauw")
								  ->setLastModifiedBy("Maarten Balliauw")
								  ->setTitle("Office 2007 PPTX Test Document")
								  ->setSubject("Office 2007 PPTX Test Document")
								  ->setDescription("Test document for Office 2007 PPTX, generated using PHP classes.")
								  ->setKeywords("office 2007 openxml php")
								  ->setCategory("Test result file");

								  // Remove first slide
echo date('H:i:s') . " Remove first slide\n";
$objPHPPowerPoint->removeSlideByIndex(0);
$currentSlide = $objPHPPowerPoint->createSlide();

/*
// Create a shape (drawing)
echo date('H:i:s') . " Create a shape (drawing)\n";
$shape = $currentSlide->createDrawingShape();
$shape->setName('PHPPowerPoint logo')
      ->setDescription('PHPPowerPoint logo')
      ->setPath('./images/phppowerpoint_logo.gif')
      ->setHeight(36)
      ->setOffsetX(10)
      ->setOffsetY(10);
$shape->getShadow()->setVisible(true)
                   ->setDirection(45)
                   ->setDistance(10);
*/

// Create a shape (text)
echo date('H:i:s') . " Create a shape (rich text)\n";
$shape = $currentSlide->createRichTextShape()
      ->setHeight(400)
      ->setWidth(600)
      ->setOffsetX(170)
      ->setOffsetY(180)
      ->setInsetTop(50)
      ->setInsetBottom(50);
$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );
$textRun = $shape->createTextRun('Thank you for using PHPPowerPoint!');
$textRun->getFont()->setBold(true)
                   ->setSize(60)
                   ->setColor( new PHPPowerPoint_Style_Color( 'FFFFFFFF' ) );
                   
// Create a shape (line)
$shape = $currentSlide->createLineShape(170, 180, 770, 180);
$shape->getBorder()->getColor()->setARGB('FFFFFFFF');

// Create a shape (line)
$shape = $currentSlide->createLineShape(170, 580, 770, 580);
$shape->getBorder()->getColor()->setARGB('FFFFFFFF');

// Create new Slide

$currentSlide = $objPHPPowerPoint->createSlide();


// Testo seconda slide
echo date('H:i:s') . " Create a shape (rich text)\n";
$shape = $currentSlide->createRichTextShape()
      ->setHeight(400)
      ->setWidth(600)
      ->setOffsetX(170)
      ->setOffsetY(180)
      ->setInsetTop(50)
      ->setInsetBottom(50);
$shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );
$textRun = $shape->createTextRun('Page 2!');
$textRun->getFont()->setBold(true)
                   ->setSize(60)
                   ->setColor( new PHPPowerPoint_Style_Color( 'FFFFFFFF' ) );

// Create new Slide (3)

$currentSlide = $objPHPPowerPoint->createSlide();

// Create a shape (table)
echo date('H:i:s') . " Create a shape (table)\n";
$shape = $currentSlide->createTableShape(22);
$shape->setHeight(200);
$shape->setWidth(940);
$shape->setOffsetX(10);
$shape->setOffsetY(300);

// Add row
echo date('H:i:s') . " Add row\n";
$row = $shape->createRow();
$row->getFill()->setFillType(PHPPowerPoint_Style_Fill::FILL_GRADIENT_LINEAR)
               ->setRotation(90)
               ->setStartColor(new PHPPowerPoint_Style_Color('FFA0A0A0'))
               ->setEndColor(new PHPPowerPoint_Style_Color('FFFFFFFF'));
$cell = $row->nextCell();
$cell->setColSpan(22);
$cell->createTextRun('Title row')->getFont()->setBold(true)
                                            ->setSize(16);

// Add row
echo date('H:i:s') . " Add row\n";
$row = $shape->createRow();
$row->getFill()->setFillType(PHPPowerPoint_Style_Fill::FILL_GRADIENT_LINEAR)
			   ->setEndColor(new PHPPowerPoint_Style_Color('FFFFFFFF'))
			   ->setStartColor(new PHPPowerPoint_Style_Color('FFFFFFFF'));

// Add row
echo date('H:i:s') . " Add row\n";
$row = $shape->createRow();
$row->nextCell()->createTextRun('R2C1');
$row->nextCell()->createTextRun('R2C2');
$row->nextCell()->createTextRun('R2C3');

// Add row
echo date('H:i:s') . " Add row\n";
$row = $shape->createRow();
$row->nextCell()->createTextRun('R3C1');
$row->nextCell()->createTextRun('R3C2');
$row->nextCell()->createTextRun('R3C3');


                   
                  
                   
                   
// Save PowerPoint 2007 file
echo date('H:i:s') . " Write to PowerPoint2007 format\n";
$objWriter = PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
$objWriter->setLayoutPack(new PHPPowerPoint_Writer_PowerPoint2007_LayoutPack_TemplateBased('Resources/template.pptx'));
$objWriter->save(str_replace('.php', '.pptx', __FILE__));

// Echo memory peak usage
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

// Echo done
echo date('H:i:s') . " Done writing file.\r\n";