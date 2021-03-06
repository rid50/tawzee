import java.awt.Color;
//import java.awt.Graphics2D;
//import java.awt.Image;
//import java.awt.RenderingHints;
//import java.awt.Transparency;
import java.awt.image.BufferedImage;
//import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;
//import java.io.PrintWriter;
import java.sql.Connection;
import java.util.HashMap;
import java.util.Locale;

import javax.imageio.ImageIO;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.log4j.Logger;

import net.sf.jasperreports.engine.JRExporterParameter;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperCompileManager;
import net.sf.jasperreports.engine.JasperFillManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.JasperPrintManager;
import net.sf.jasperreports.engine.JasperReport;
import net.sf.jasperreports.engine.design.JasperDesign;
import net.sf.jasperreports.engine.export.JRGraphics2DExporter;
import net.sf.jasperreports.engine.export.JRGraphics2DExporterParameter;
import net.sf.jasperreports.engine.export.JRPdfExporter;
import net.sf.jasperreports.engine.xml.JRXmlLoader;
import net.sf.jasperreports.export.SimpleExporterInput;
import net.sf.jasperreports.export.SimpleOutputStreamExporterOutput;
import net.sf.jasperreports.export.SimplePdfExporterConfiguration;

import com.JDesignerExtension;
import com.JasperReportsWrapper;

public class JasperServlet extends HttpServlet {
	private static final long serialVersionUID = 4350549139109004305L;

	private static final Logger LOG = Logger.getLogger(JasperServlet.class);
	
	public void doGet(HttpServletRequest request, HttpServletResponse response)	throws ServletException, IOException {
		go(request, response);
	}

	public void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		go(request, response);
	}
	
	void go(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		try {
			if (request.getParameter("CheckConnection") != null) {
				response.addHeader("Access-Control-Allow-Origin", "*");
				response.addHeader("Access-Control-Allow-Methods",
						"POST, GET, OPTIONS, PUT, DELETE, HEAD");
				response.addHeader("Access-Control-Allow-Headers",
						"X-PINGOTHER, Origin, X-Requested-With, Content-Type, Accept");
				response.addHeader("Access-Control-Max-Age", "1728000");
				return;
			}
			
			String rName = request.getParameter("reportName");
			String applicationNumber = request.getParameter("applicationNumber");
			String keyFieldValue = request.getParameter("keyFieldValue");
			String renderAs = request.getParameter("renderAs");

			//LOG.info("****rname: " + rName);
			
			// Connect to DB and compile JRXML file
			JasperReportsWrapper wrapper = new JasperReportsWrapper();
			Connection conn = wrapper.getConnection();

	        if (conn == null) {
	            //System.out.println("Connection failed!");
				throw new Exception("Connection failed!");
	            //return;
	        }
/*			
			Connection conn = wrapper.connect2DB(
					JasperReportsWrapper.dbServerAdd,
					JasperReportsWrapper.dbServerPort,
					JasperReportsWrapper.dbName, JasperReportsWrapper.dbUser,
					JasperReportsWrapper.dbPass);
*/
			// String filePath = getServletContext().getRealPath(rName +  ".jasper");
			// JasperReport report = (JasperReport)JRLoader.loadObjectFromFile(filePath);

			String filePath = getServletContext().getRealPath(rName + ".jrxml");
			// JasperReport report = wrapper.compileJRXMLFile(filePath);

			HashMap<String, Object> parameters = new HashMap<String, Object>();
			parameters.put("ApplicationNumber", applicationNumber);

			JasperDesign design = JRXmlLoader.load(filePath);
			
			if (renderAs.equals("pdf")) {
				parameters.put("KeyFieldValue", keyFieldValue);
				parameters.put("ReportName", rName);
				JDesignerExtension jd = new JDesignerExtension(conn);
				// jd.addParameters(parameters);
				//jd.addImages(design, parameters, request);
				jd.addImages(design, parameters, request.getServerName());
			}
			
			JasperReport report = JasperCompileManager.compileReport(design);

			Locale locale = new Locale("ar", "KW");
			parameters.put(JRParameter.REPORT_LOCALE, locale);

			// parameters.put(JRParameter.REPORT_LOCALE, Locale.AR);
			// Fill compiled JRXML file with data
			JasperPrint print = wrapper.fillReport(report, parameters);
			//JasperPrint print = wrapper.fillReport(report, parameters, wrapper.getConnection());
			//JasperPrint print = JasperFillManager.fillReport(report, parameters, wrapper.getConnection());

			if (renderAs.equals("png")) {
				response.setContentType("image/png");

				//Image image = JasperPrintManager.printPageToImage(print, 0, 2.0f);
				BufferedImage image = new BufferedImage(
						print.getPageWidth() + 1, print.getPageHeight() + 1,
						BufferedImage.TYPE_INT_RGB);

				// Graphics2D gr2 = (Graphics2D) image.getGraphics();
				// gr2.setColor(Color.white);
				// gr2.fillRect(0, 0, print.getPageWidth() + 1,
				// print.getPageHeight() + 1);
				// gr2.setRenderingHint(RenderingHints.KEY_TEXT_ANTIALIASING,
				// RenderingHints.VALUE_TEXT_ANTIALIAS_GASP);
				// gr2.setRenderingHint(RenderingHints.KEY_TEXT_LCD_CONTRAST,100);
				// gr2.setRenderingHint(RenderingHints.KEY_ANTIALIASING,
				// RenderingHints.VALUE_ANTIALIAS_ON);
				// gr2.setRenderingHint(RenderingHints.KEY_RENDERING,
				// RenderingHints.VALUE_RENDER_QUALITY);

				// image = (BufferedImage)
				// JasperPrintManager.printPageToImage(print, 0, 2.0f);

				JRGraphics2DExporter exporter = new JRGraphics2DExporter();
				exporter.setParameter(JRExporterParameter.JASPER_PRINT, print);
				exporter.setParameter(
						JRGraphics2DExporterParameter.GRAPHICS_2D,
						image.getGraphics());
				exporter.setParameter(JRExporterParameter.PAGE_INDEX,
						new Integer(0));
				// exporter.setParameter(JRExporterParameter.OFFSET_X, new
				// Integer(1)); // lblPage & border
				// exporter.setParameter(JRExporterParameter.OFFSET_Y, new
				// Integer(1));
				//LOG.info("****1: " + (print.getPageWidth() + 1) + " ----- " + (print.getPageHeight() + 1));

				OutputStream out = response.getOutputStream();
				exporter.setParameter(JRExporterParameter.OUTPUT_STREAM, out);

				// OutputStream out = new FileOutputStream("kuku-test.jpg");
				// exporter.setParameter(JRExporterParameter.OUTPUT_FILE, out);

				exporter.exportReport();
				
				ImageIO.write((BufferedImage) image, "png", out);
				//out.close();
			} else {
				//PrintWriter out = response.getWriter();
				//out.print("kuku");
				//if (true)
				//	return;
				response.setContentType("application/pdf");

				// Export PDF file to browser window
				JRPdfExporter exporter = new JRPdfExporter();

				exporter.setExporterInput(new SimpleExporterInput(print));
				exporter.setExporterOutput(new SimpleOutputStreamExporterOutput(response.getOutputStream()));
				SimplePdfExporterConfiguration configuration = new SimplePdfExporterConfiguration();
				exporter.setConfiguration(configuration);				
/*				
				exporter.setParameter(JRExporterParameter.CHARACTER_ENCODING,
						"UTF-8");
				exporter.setParameter(JRExporterParameter.JASPER_PRINT, print);
				exporter.setParameter(JRExporterParameter.OUTPUT_STREAM,
						response.getOutputStream());
*/						
				exporter.exportReport();
			}
		} catch (Exception e) {
			//LOG.info("Exception: " + e.getMessage());
			
			response.setContentType("image/png");
			
			//GraphicsEnvironment ge = GraphicsEnvironment.getLocalGraphicsEnvironment();
		    //GraphicsDevice gs = ge.getDefaultScreenDevice();
		    //GraphicsConfiguration gc = gs.getDefaultConfiguration();

		    // Create an image that does support transparency
		    //BufferedImage image = gc.createCompatibleImage(600, 600, Transparency.TRANSLUCENT);

			
			BufferedImage image = new BufferedImage(600, 600, BufferedImage.TYPE_INT_ARGB);
			java.awt.Graphics graphics = image.getGraphics();
	        //graphics.setColor(Color.OPAQUE);
	        //graphics.fillRect(0, 0, 200, 200);
	        graphics.setColor(Color.RED);
	        graphics.setFont(new java.awt.Font("Arial", java.awt.Font.BOLD, 12));
	        String errtext = e.getMessage();
	        int i; if ((i = errtext.indexOf("Access denied")) != -1)
	        	errtext = errtext.substring(0, i + ("Access denied").length());  
	        graphics.drawString(errtext, 10, 20);
			OutputStream out = response.getOutputStream();
			ImageIO.write((BufferedImage) image, "png", out);
	        
			
			//response.setContentType("text/html");
			//PrintWriter out = response.getWriter();
			//out.print(e.toString());
			
			//e.printStackTrace(System.out);
		}
	}
}
