package com;

import java.awt.Color;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;

import javax.servlet.http.HttpServletRequest;

import org.apache.log4j.Logger;

//import com.rid50.reports.util.JrUtils;

import net.sf.jasperreports.charts.type.ScaleTypeEnum;
import net.sf.jasperreports.engine.JRBand;
import net.sf.jasperreports.engine.JRChild;
import net.sf.jasperreports.engine.JRComponentElement;
import net.sf.jasperreports.engine.JRElement;
import net.sf.jasperreports.engine.JRElementGroup;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRPropertiesHolder;
import net.sf.jasperreports.engine.JRPropertiesMap;
import net.sf.jasperreports.engine.JRSection;
import net.sf.jasperreports.engine.component.Component;
import net.sf.jasperreports.engine.design.JRDesignBand;
import net.sf.jasperreports.engine.design.JRDesignComponentElement;
import net.sf.jasperreports.engine.design.JRDesignExpression;
import net.sf.jasperreports.engine.design.JRDesignFrame;
import net.sf.jasperreports.engine.design.JRDesignImage;
import net.sf.jasperreports.engine.design.JRDesignParameter;
import net.sf.jasperreports.engine.design.JRDesignStyle;
import net.sf.jasperreports.engine.design.JasperDesign;
import net.sf.jasperreports.engine.type.HorizontalAlignEnum;
import net.sf.jasperreports.engine.type.ModeEnum;
import net.sf.jasperreports.engine.type.PositionTypeEnum;
import net.sf.jasperreports.engine.type.ScaleImageEnum;
import net.sf.jasperreports.engine.type.StretchTypeEnum;
import net.sf.jasperreports.engine.type.VerticalAlignEnum;

public class JDesignerExtension {
	public static final int JAPER_REPORTS_DPI = 72;

	private static final Logger LOG = Logger.getLogger(JDesignerExtension.class);
	
    private Connection _connection = null;
    
	public JDesignerExtension(Connection conn) {
		_connection = conn;
	}

	//public void addImages(JasperDesign design, HashMap<String, Object> parameters, HttpServletRequest request) throws Exception {
	public void addImages(JasperDesign design, HashMap<String, Object> parameters, String reqUrl) throws Exception {
		
		JRDesignParameter parameter = null;
		JRDesignExpression expression = null;
		JRDesignImage image = null;
		
        String tableName;
	    String keyFieldName;
	    String keyFieldValue;
		
        int signatureID;
        float topPos;
        float leftPos;
        int width, height, resolution;
        //int bottomY;
        //boolean scale;
        short imageYPosOffset;
        
		//List<JRDesignImage> imageList = new ArrayList();

		keyFieldValue = (String)parameters.get("KeyFieldValue");
        
		//String rn = (String)parameters.get("ReportName");
		if (((String)parameters.get("ReportName")).equals("TawzeeApplicationForm")) {
			tableName = "ApplicationSignature";
			keyFieldName = "ApplicationNumber";
			//imageYPosOffset = 20;
		} else {
			tableName = "ApplicationLoadSignature";
			keyFieldName = "FileNumber";
			//imageYPosOffset = 30;
		}
		
		imageYPosOffset = 20;

		String parameterName = "";
		//int reportHeight = 0;
		JRBand[] allBands;
//		
//		//JRBand columnHeader = design.getColumnHeader();
//		allBands = design.getAllBands();
//		for (JRBand ban : allBands) {
//			reportHeight += ban.getHeight();
//			LOG.info("Height: " + ban.getHeight() + " , ReportHeight" + reportHeight);
//		}

		//if (true)
		//	throw new Exception(reqUrl);
		
//		LOG.info("Total Height: " + reportHeight);
//		LOG.info("Page width: " + design.getPageWidth());
		
//		JRSection sect = design.getDetailSection();
//
//		JRBand[] bands = sect.getBands();
//		for (JRBand ban : bands) {
//			LOG.info("Band Height: " + ban.getHeight());
//		}

		
//		JRDesignBand bgrBand = new JRDesignBand();
//		//band.setHeight(reportHeight + 500 * 72 /300);
//		bgrBand.setHeight(design.getPageHeight() - design.getTopMargin() - design.getBottomMargin());

//		JRDesignFrame frame = new JRDesignFrame();
//	    //frame.setX(0);
//	    //frame.setY(0);
//	    frame.setWidth(design.getPageWidth());
//	    frame.setHeight(reportHeight);
//	    frame.setBackcolor(new Color(0xff, 0x00, 0x00));
//	    frame.setMode(ModeEnum.TRANSPARENT);
//	    band.addElement(frame);
		
	    //JRBand aband = design.getPageHeader();
	    //java.util.List<JRChild> list = aband.getChildren();
		
		try {
			// statements allow to issue SQL queries to the database
		    PreparedStatement preparedStatement =
		    	_connection.prepareStatement("SELECT SignatureID, TopPos, LeftPos, Width, Height, Resolution FROM " + tableName + " INNER JOIN SignatureList ON SignatureID = ID WHERE " + keyFieldName + "  =  ? ");
		    preparedStatement.setString(1, keyFieldValue);
		    ResultSet resultSet = preparedStatement.executeQuery();
		    //int count = 0;
		    while (resultSet.next()) {
				//LOG.info("SignatureID: " + resultSet.getInt("SignatureID"));
		    	
		        signatureID = resultSet.getInt("SignatureID");
		        topPos = resultSet.getFloat("TopPos");
		        leftPos = resultSet.getFloat("LeftPos");
		        width = resultSet.getInt("Width");
		        height = resultSet.getInt("Height");
		        resolution = resultSet.getInt("Resolution");
		        		        
				parameter = new JRDesignParameter();
		        parameterName = "s" + Integer.toString(signatureID);
				parameter.setName(parameterName);
				parameter.setValueClass(java.awt.Image.class);

				design.addParameter(parameter);

				////parameters.put(parameterName, com.rid50.reports.util.JrUtils.getSignature(Integer.toString(signatureID), request));

				//parameters.put(parameterName, com.rid50.reports.util.JrUtils.getSignature(Integer.toString(signatureID), reqUrl));
				
				//scale = false;

				image = new JRDesignImage(design);
				
				//LOG.info("image: " + image);
				
				expression = new JRDesignExpression();
				expression.setText("$P{s" + Integer.toString(signatureID) + "}");
				image.setExpression(expression);
				
				//image.setPositionType(PositionTypeEnum.FIX_RELATIVE_TO_TOP);
				image.setX(Math.round(leftPos) - design.getLeftMargin() + 2);
				int imagePosY = Math.round(topPos) - design.getTopMargin();
				//int imagePosY = Math.round(topPos);
				int imageHeight = height * JAPER_REPORTS_DPI / resolution;
				//image.setY(imagePosY);
				image.setHeight(imageHeight);
				image.setWidth(width * JAPER_REPORTS_DPI / resolution);
				//image.setScaleImage(ScaleImageEnum.RETAIN_SHAPE);

				//parameters.put(parameterName, com.rid50.reports.util.JrUtils.getSignature(Integer.toString(signatureID), scale, resolution));
				
				//JRDesignStyle style = new JRDesignStyle();
				//style.setDefault(true);
				//style.setBackcolor(new Color(0xff, 0x00, 0x00));
				
				//image.setStyle(style);
				
				//image.setMode(ModeEnum.OPAQUE);
				//image.setBackcolor(new Color(0xff, 0x00, 0x00));

				//LOG.info("ModeValue: " + image.getModeValue());	// default TRANSPARENT

				//image.setScaleImage(ScaleImageEnum.CLIP);
				//image.setStretchType(StretchTypeEnum.NO_STRETCH);

				//LOG.info("ScaleImageValue: " + image.getScaleImageValue());	// default RETAIN_SHAPE
				//LOG.info("StretchTypeValue: " + image.getStretchTypeValue());	// default NO_STRETCH

				int diffRunTimeDesignDetailBandHeight = 0;
				int bandPosY = 0; //boolean done = false;
				int tableRowHeight = 0;
				JRExpression exp;
				allBands = design.getAllBands();
				int numOfBands = allBands.length;
				for (JRBand ban : allBands) {
					numOfBands--;

					java.util.List<JRChild> list = ban.getChildren();

					exp = ban.getPrintWhenExpression();
					if (exp != null && exp.getText().equalsIgnoreCase("false")) {
						bandPosY += ban.getHeight();
						continue;
					}

					//LOG.info("");
					//LOG.info("bandPosY: " + bandPosY + " --- " + "ban.getHeight(): " + ban.getHeight());
					
					JRPropertiesMap prm = ban.getPropertiesMap();
					if (prm != null &&
						prm.getProperty("section_name") != null &&
						prm.getProperty("section_name").equalsIgnoreCase("detail")) {
						//LOG.info("SectionName: detail");
						tableRowHeight = imageYPosOffset;
						
						JRDesignBand pageColumnFooter = (JRDesignBand)design.getColumnFooter();
						JRDesignBand pageFooterBand = (JRDesignBand)design.getPageFooter();
						//pageHeaderHeight = pageHeaderBand.getHeight();
						diffRunTimeDesignDetailBandHeight = design.getPageHeight() - 
									design.getTopMargin() - 
									design.getBottomMargin() -
									pageColumnFooter.getHeight() -
									pageFooterBand.getHeight() -
									bandPosY - ban.getHeight();
						
						//LOG.info("runTimeDetailBandHeight: " +  diffRunTimeDesignDetailBandHeight);
						/*
						LOG.info("design.getPageHeight(): " + design.getPageHeight());
						LOG.info("design.getTopMargin(): " + design.getTopMargin());
						LOG.info("design.getBottomMargin(): " + design.getBottomMargin());
						LOG.info("pageHeaderBand.getHeight(): " + pageFooterBand.getHeight());
						//LOG.info("banHeight: " + banHeight);
						*/
						//JRElementGroup gr = ban.getElementGroup();
						//JRElement[] els = ban.getElements();
					    //java.util.List<JRChild> list = ban.getChildren();
/*
						for (JRElement el0 : ban.getElements()) {
							Component comp = ((JRComponentElement)el0).getComponent();
							JRElementGroup grp = el0.getElementGroup();
							
							for (JRElement el : grp.getElements()) {
								LOG.info("Class: " + el.getClass());
								LOG.info("Key: " + el.getKey());
								LOG.info("Height: " + el.getHeight());
								LOG.info("ToString: " + el.toString());
							}
							
//							LOG.info("ElementGroup: " + cel.getElementGroup());
							
						}
*/
						//LOG.info("Elements: " + els);
						//LOG.info("ElementGroup: " + gr);
						
						//JRElement el = ban.getElementByKey("jr:detailCell");
						//if (el != null) {
						//	columnHeight = el.getHeight();
//						//	LOG.info("ColumnHeight: " + columnHeight);
						//}
						//columnHeight = 20;
						//LOG.info("SectionNum: " + columnHeight);
					}

//					LOG.info("BandPosY: " + bandPosY + " , ImagePosY: " + imagePosY + " , Height: " + ban.getHeight());
					
					//imagePosY = imagePosY - bandPosY - (tableRowHeight * 2);
					//imagePosY = imagePosY - bandPosY;
					//if (imagePosY + imageHeight <= ban.getHeight()) {

					if (imagePosY - bandPosY + imageHeight <= ban.getHeight()) {
					//if (imagePosY - bandPosY + imageHeight <= banHeight) {
					//if (imagePosY - bandPosY + imageHeight <= ban.getHeight()) {
						//JRPropertiesHolder prh = ban.getParentProperties();
						//JRElement el = ban.getElementByKey("jr:columnHeader");
						//if (el != null)
						//	LOG.info("ColumnHeight: " + columnHeight);
						//columnHeight = el.getHeight();

						//if (bandPosY > imagePosY) {
						//imagePosY = imagePosY - bandPosY - (tableRowHeight * 2);
						//image.setY(imagePosY);
						//image.setY(imagePosY - bandPosY - (tableRowHeight * 2) + 4);
						if (numOfBands > 1)
							image.setY(imagePosY - bandPosY);
						else
							image.setY(imagePosY - bandPosY - diffRunTimeDesignDetailBandHeight);
						
						parameters.put(parameterName, com.rid50.reports.util.JrUtils.getSignature(Integer.toString(signatureID), reqUrl));
						
						/*
						LOG.info("===================");
						LOG.info("imagePosY: " + imagePosY);
						LOG.info("imageHeight: " + imageHeight);
						LOG.info("bandPosY: " + bandPosY);
						LOG.info("tableRowHeight: " + tableRowHeight);
						LOG.info("banHeight: " + ban.getHeight());
						LOG.info("imagePosY - bandPosY + imageHeight: " + (imagePosY - bandPosY + imageHeight));
						//LOG.info("imagePosY - bandPosY - (tableRowHeight * 2) + 4: " + (imagePosY - bandPosY - (tableRowHeight * 2) + 4));
						*/
						//LOG.info("BandPosY: " + bandPosY + " , ImagePosY: " + imagePosY + " , Height: " + ban.getHeight());

						//image.setY(imagePosY);
						
						//LOG.info("RealImagePosY: " + image.getY());
						

						//image.setY(imagePosY - bandPosY);
						//image.setY(imagePosY - (bandPosY - ban.getHeight()));
						list.add(image);
						//done = true;
						//LOG.info("RealImagePosY: " + image.getY() + " , BandPosY: " + bandPosY + " , ImagePosY: " + imagePosY + " , Height: " + ban.getHeight());
						//LOG.info("yes");
						//parameters.put(parameterName, com.rid50.reports.util.JrUtils.getSignature(Integer.toString(signatureID), reqUrl));
						
						break;
					} else {
						//LOG.info("numOfBands: " + numOfBands);
						
						if (numOfBands == 1) {
						//imagePosY = imagePosY - bandPosY;
							int imageBottom = imagePosY + imageHeight;
							//LOG.info("ImageBottom: " + (imagePosY + imageHeight) + " , BandPosY: " + bandPosY + " , ImagePosY: " + imagePosY + " , Height: " + ban.getHeight());
							
							//if (imageBottom - bgrBand.getHeight() > 0) {
							if (imageBottom - (bandPosY + ban.getHeight()) > 0) {
								int delta = imageBottom - diffRunTimeDesignDetailBandHeight - (bandPosY + ban.getHeight());
								if (delta > 0) {
									parameters.put(parameterName, com.rid50.reports.util.JrUtils.getSignature(Integer.toString(signatureID), true, resolution, reqUrl));
									
									//imagePosY -= delta;
//									ban.(ban.getHeight() + delta + 10);
									
									imageHeight = (height - (height * delta / imageHeight)) * JAPER_REPORTS_DPI / resolution;
									//image.setX(0);
									//image.setY(-500);
									//image.setWidth(width);
									//image.setHeight(height);
									image.setHeight(imageHeight);
									//image.setLazy(false);
									//image.setHorizontalAlignment(HorizontalAlignEnum.CENTER);
									//image.setVerticalAlignment(VerticalAlignEnum.TOP);
									image.setScaleImage(ScaleImageEnum.CLIP);
									
								} else
									parameters.put(parameterName, com.rid50.reports.util.JrUtils.getSignature(Integer.toString(signatureID), reqUrl));
								
								//image.setY(imagePosY - bandPosY - (tableRowHeight * 2) - delta);
								
								image.setY(imagePosY - bandPosY - diffRunTimeDesignDetailBandHeight);
								
								LOG.info("imageHeight: " + image.getHeight());
								LOG.info("imageWidth: " + image.getWidth());								
								
								/*
								LOG.info("****************************");
								
								//LOG.info("Delta: " + delta + " , getY(): " + image.getY());
								LOG.info("diffRunTimeDesignDetailBandHeight: " + diffRunTimeDesignDetailBandHeight);
								
								LOG.info("imagePosY: " + imagePosY);
								//LOG.info("imageHeight: " + imageHeight);
								LOG.info("imageBottom: " + imageBottom);
								LOG.info("bandPosY: " + bandPosY);
								LOG.info("ban.getHeight(): " + ban.getHeight());
								*/
								/*
								LOG.info("height: " + height);
								LOG.info("imageHeight: " + imageHeight);
								LOG.info("delta: " + delta);
								LOG.info("design.getPageHeight(): " + design.getPageHeight());
								LOG.info("image.getScaleImageValue(): " + image.getScaleImageValue());
								//LOG.info("imagePosY - bandPosY - delta: " + (imagePosY - bandPosY - delta));
								*/
								
								list.add(image);
								
								//image.setY(imagePosY - (imageBottom - bgrBand.getHeight()));
								//image.setHeight(height * JAPER_REPORTS_DPI / resolution - (bottomY - band.getHeight()));
								//image.setScaleImage(ScaleImageEnum.CLIP);
								//scale = true;
								//done = true;
								//LOG.info("no");
								//break;
							}
						}

						//bandPosY += banHeight;
						bandPosY += ban.getHeight();
						//else {
							//if (imagePosY == 745)
							//	imagePosY -= 50;
							
						//	image.setY(imagePosY);
						//}
					}
				}
				
				
				//LOG.info("Done: " + done);
/*				
				if (!done) {
					bottomY = image.getY() + imageHeight;
					if (bottomY - bgrBand.getHeight() > 0) {
						image.setY(imagePosY - (bottomY - bgrBand.getHeight()));
						//image.setHeight(height * JAPER_REPORTS_DPI / resolution - (bottomY - band.getHeight()));
						//image.setScaleImage(ScaleImageEnum.CLIP);
						//scale = true;
					} else 
						image.setY(imagePosY);
				
					bgrBand.addElement(image);
				}
*/				
				//list.add(image);
				
				//band.addElement(image);
		    }

			JRDesignBand bgrBand = (JRDesignBand) design.getBackground();
			bgrBand.setHeight(design.getPageHeight() - design.getTopMargin() - design.getBottomMargin());

			expression = new JRDesignExpression();
			expression.setText("true");
			bgrBand.setPrintWhenExpression(expression);

			expression = new JRDesignExpression();
			expression.setText("\"images/background.png\"");
			
			image = new JRDesignImage(design);
			image.setExpression(expression);
			image.setX(0);
			image.setY(0);
			image.setWidth(design.getPageWidth());
			image.setHeight(design.getPageHeight() - design.getTopMargin() - design.getBottomMargin());

			java.util.List<JRChild> list = bgrBand.getChildren();
			list.add(image);
		    
			//LOG.info("Band Height: " + band.getHeight());
		    
//			design.setBackground(bgrBand);
			
//		    HashMap<String, Object> para = new HashMap<String, Object>();
//		    for (Entry<String, Object> entry : parameters.entrySet()) {
//		    	LOG.info("Key: = " + entry.getKey());
//		    	LOG.info("Value: = " + entry.getValue());
//		    }
		    
//		    Iterator it = parameters.entrySet().iterator();
//		    while (it.hasNext()) {
//		    	Entry<String, Object> pairs = (Entry<String, Object>) it.next();
//		    	LOG.info(pairs.getKey() + " = " + pairs.getValue());
//		        //it.remove(); // avoids a ConcurrentModificationException
//		    }

//		    for (JRDesignImage image : imageList) {
//		    	LOG.info("Expression: = " + image.getExpression().getText());
//		    }
		    

//			JRBand bband = design.getBackground();
//		    java.util.List<JRChild> list = bband.getChildren();
//		    for (JRChild child : list) {
//		    	LOG.info("Child: " + child.toString());
//		    	LOG.info("Image->Expression: " + ((JRDesignImage)child).getExpression().getText());
//		    	LOG.info("Image->X pos: " + ((JRDesignImage)child).getX());
//		    	LOG.info("Image->Y pos: " + ((JRDesignImage)child).getY());
//		    	LOG.info("Image->width: " + ((JRDesignImage)child).getWidth());
//		    	LOG.info("Image->height: " + ((JRDesignImage)child).getHeight());		    	
//		    }
		} catch (Exception e) {
			//LOG.info("Error: " + e.toString());
			throw new Exception(e.toString());
		}
	}
	
//	public void addParameters(HashMap<String, Object> parameters) throws Exception {
//		
//		//_applicationNumber = (String)parameters.get("ApplicationNumber");
//				
//		parameters.put("s12", com.rid50.reports.util.JrUtils.getSignature("236"));
//		parameters.put("s13", com.rid50.reports.util.JrUtils.getSignature("236"));
//		parameters.put("s14", com.rid50.reports.util.JrUtils.getSignature("236"));
//	}
	
}