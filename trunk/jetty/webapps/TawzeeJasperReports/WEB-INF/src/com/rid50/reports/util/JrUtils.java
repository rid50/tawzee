package com.rid50.reports.util;

import java.awt.Graphics2D;
import java.awt.Image;
import java.awt.RenderingHints;
import java.awt.image.BufferedImage;
import java.awt.image.IndexColorModel;
import java.awt.image.RenderedImage;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.InputStream;
import java.net.InetAddress;
import java.net.URL;
import javax.imageio.ImageIO;
import javax.servlet.http.HttpServletRequest;

import org.apache.log4j.Logger;
//import java.io.FileInputStream;

public class JrUtils
{
	public static final int JAPER_REPORTS_DPI = 72;
	
	//private static final Logger LOG = Logger.getLogger(JrUtils.class);
	
	public static boolean isCheckboxMatch(Integer value, int bitmask) throws Exception {
		// if (value != -1) {
		// for (short i = 0; i < bitmask - 1; i++) {
		// if ((value & bitmask) != 0)
		// return true;
		// bitmask >>= 1;
		// }
		// }

		//try {
			// Log log4j configuration
	        /*
			final Properties log4jProperties = new Properties();
	        log4jProperties.load(JrUtils.class.getResourceAsStream("log4j.properties"));
	        PropertyConfigurator.configure(log4jProperties);

	        LOG.info("run - isCheckboxMatch");
			*/
	        
	        return value != -1 && (value & bitmask) != 0;
		//} catch (Exception e) {
			//throw new Exception(e.toString()); 
			//e.printStackTrace();
		//}
		//return true;
	}

	//public static BufferedImage getSignature(String id, HttpServletRequest request) throws Exception {
	public static BufferedImage getSignature(String id, String reqUrl) throws Exception {
		return getSignature(id, false, 0, reqUrl);
	}
	
	//public static BufferedImage getSignature(String id, boolean scale, int resolution, HttpServletRequest request) throws Exception {
	public static BufferedImage getSignature(String id, boolean scale, int resolution, String reqUrl) throws Exception {
		try {
			// Log log4j configuration
			/*
			final Properties log4jProperties = new Properties();
	        log4jProperties.load(JrUtils.class.getResourceAsStream("log4j.properties"));
	        PropertyConfigurator.configure(log4jProperties);
			*/
			
			//String reqUrl = request.getServerName();
			//if (request.getContextPath() != "")
			//	reqUrl += "/" + request.getContextPath();
			
			//String hostName = InetAddress.getLocalHost().getHostName();
			//URL url = new URL("http://" + reqUrl + "/my_fopen.php?id=" + id);
			//URL url = new URL("http://" + reqUrl + "/get_image.php?userAgent=jetty&id=" + id);
			URL url = new URL("http://" + reqUrl + "/get_image.php?id=" + id);

			//LOG.info("Id: " + id);
			
			// Read the image ...
			InputStream inputStream = url.openStream();

			ByteArrayOutputStream baos = new ByteArrayOutputStream();
			byte[] buffer = new byte[1024];

			int n = 0;
			while (-1 != (n = inputStream.read(buffer))) {
				baos.write(buffer, 0, n);
			}

			baos.flush();
			byte[] imageInByte = baos.toByteArray();
			baos.close();

			// convert byte array back to BufferedImage
			InputStream in = new ByteArrayInputStream(imageInByte);

			BufferedImage bImage = ImageIO.read(in);
			
            //ImageIO.write(bImage, "png", new File("bImage.png"));
			
			if (scale) {
				BufferedImage scaledBufImage = new BufferedImage(bImage.getWidth() * JAPER_REPORTS_DPI / resolution, bImage.getHeight() * JAPER_REPORTS_DPI / resolution, bImage.getTransparency()); // BufferedImage.TRANSLUCENT
	            Graphics2D g2d = (Graphics2D)scaledBufImage.createGraphics();
	            g2d.setRenderingHint(RenderingHints.KEY_TEXT_ANTIALIASING, RenderingHints.VALUE_TEXT_ANTIALIAS_ON);
	            g2d.setRenderingHint(RenderingHints.KEY_INTERPOLATION, RenderingHints.VALUE_INTERPOLATION_BICUBIC);
	            g2d.setRenderingHint(RenderingHints.KEY_RENDERING, RenderingHints.VALUE_RENDER_QUALITY);
	            g2d.setRenderingHint(RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);
	            g2d.setRenderingHint(RenderingHints.KEY_COLOR_RENDERING, RenderingHints.VALUE_COLOR_RENDER_QUALITY);
	            
	            g2d.drawImage(bImage, 0, 0, bImage.getWidth() * JAPER_REPORTS_DPI / resolution, bImage.getHeight() * JAPER_REPORTS_DPI / resolution, null);
	
	            ImageIO.write(scaledBufImage, "png", new File("scaledBufImage.png"));

				//Image bImage2 = bImage.getScaledInstance(677 * 72 /300, 500 * 72 /300, Image.SCALE_SMOOTH);
				//LOG.info("Height: " + bImage.getHeight());
				//LOG.info("Width: " + bImage.getWidth());
				//return bImage;
	            g2d.dispose();
	            
				return scaledBufImage;
			} else {
	            ImageIO.write(bImage, "png", new File("notScaledBufImage.png"));
				return bImage;
			}

		} catch (Exception e) {
			throw new Exception(e.toString()); 
		}

		//return null;
	}
}