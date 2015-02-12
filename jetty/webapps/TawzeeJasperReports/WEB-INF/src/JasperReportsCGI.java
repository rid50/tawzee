import java.awt.GraphicsEnvironment;
import java.awt.image.BufferedImage;
import java.io.IOException;
import java.nio.charset.Charset;
import java.sql.Connection;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;

import javax.imageio.ImageIO;

import net.sf.jasperreports.engine.JRExporterParameter;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperCompileManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.JasperReport;
import net.sf.jasperreports.engine.design.JasperDesign;
import net.sf.jasperreports.engine.export.JRGraphics2DExporter;
import net.sf.jasperreports.engine.export.JRGraphics2DExporterParameter;
import net.sf.jasperreports.engine.export.JRPdfExporter;
import net.sf.jasperreports.engine.xml.JRXmlLoader;

import org.apache.http.NameValuePair;
import org.apache.http.client.utils.URLEncodedUtils;
import org.apache.log4j.Logger;

import com.JDesignerExtension;
import com.JasperReportsWrapper;

public class JasperReportsCGI {
	private static final long serialVersionUID = 4350549139109004305L;
	
	//private static final Logger LOG = Logger.getLogger(JasperReportsCGI.class);

	public static void main( String args[] ) {
		String server_name = System.getProperty("cgi.server_name");
		String query_string = System.getProperty("cgi.query_string");
		if (query_string.equals("CheckConnection")) {
			//System.out.println("Content-Type: text/html\n\n");
			System.out.println("Ok");
			return;
		}
			
		//System.out.println(server_name);
		//System.out.println(query_string);
		//String query_string = "reportName=TawzeeApplicationForm&applicationNumber=12345&keyFieldValue=12345&renderAs=png";
		//String url = "http://www.example.com/something.html?one=11111&two=22222&three=33333";
		List<NameValuePair> params = URLEncodedUtils.parse(query_string, Charset.forName("UTF8"));
		Map<String, String> map = new HashMap<String, String>();
		for (NameValuePair param : params) {
			map.put(param.getName(), param.getValue());
			//System.out.println(param.getName() + " : " + param.getValue());
		}
		
		//System.out.println("Content-Type: text/html\n\n");
		//System.out.println(map.get("reportName"));
        //if (true)
        //	return;
		
/*		
    	String path = System.getProperty("gnu.classpath.boot.library.path");
        path = path.substring(0, path.lastIndexOf('/', path.length()));
    	System.setProperty("java.library.path", path + "/amd64");
    	System.setProperty("java.library.path", "/var/www/java-se-7-ri/jre/lib/amd64");    	

    	System.out.println("<br/>java.class.path: " + System.getProperty("java.class.path") + "<br/>");
        System.out.println("java.library.path: " + System.getProperty("java.library.path") + "<br/>");
        System.out.println("sun.boot.class.path: " + System.getProperty("sun.boot.class.path") + "<br/>");
        System.out.println("gnu.classpath.boot.library.path: " + System.getProperty("gnu.classpath.boot.library.path") + "<br/>");
*/		
		
		try {
/*			
			if (request.getParameter("CheckConnection") != null) {
				response.addHeader("Access-Control-Allow-Origin", "*");
				response.addHeader("Access-Control-Allow-Methods",
						"POST, GET, OPTIONS, PUT, DELETE, HEAD");
				response.addHeader("Access-Control-Allow-Headers",
						"X-PINGOTHER, Origin, X-Requested-With, Content-Type, Accept");
				response.addHeader("Access-Control-Max-Age", "1728000");
				return;
			}
*/			
			//String rName = request.getParameter("reportName");
			//String applicationNumber = request.getParameter("applicationNumber");
			//String keyFieldValue = request.getParameter("keyFieldValue");
			//String renderAs = request.getParameter("renderAs");

			String rName = map.get("reportName");
			String applicationNumber = map.get("applicationNumber");
			String keyFieldValue = map.get("keyFieldValue");
			String renderAs = map.get("renderAs");
			
			// Connect to DB and compile JRXML file
			JasperReportsWrapper wrapper = new JasperReportsWrapper();
			Connection conn = wrapper.getConnection();
			
	        if (conn == null) {
				System.out.println("Content-Type: text/html\n\n");
	            System.out.println("Connection failed!");
	            return;
	        }

	        String path = JasperReportsCGI.class.getClassLoader().getResource("JasperReportsCGI.class").getPath();
	        //path = path.substring(0, path.lastIndexOf('/', path.length() - 2));
	        path = path.substring(0, path.lastIndexOf('/', path.length()));
	        path = path.substring(0, path.lastIndexOf('/', path.length()));
	        path = path.substring(0, path.lastIndexOf('/', path.length()));
	        
			//System.out.println("Content-Type: text/html\n\n");
	        //System.out.println(path);
	        //System.out.println("java.class.path: " + System.getProperty("java.class.path"));
	        //System.out.println("sun.boot.class.path: " + System.getProperty("sun.boot.class.path"));
	        //if (true)
	        //	return;
	        
			//String filePath = getServletContext().getRealPath(rName + ".jrxml");
			String filePath = path + "/" + rName + ".jrxml";

			//System.out.println("Content-Type: text/html\n\n");
		    //System.out.println("Headless mode: " + GraphicsEnvironment.isHeadless());	        
	        //System.out.println("PATH: " + JasperReportsCGI.class.getClassLoader().getResource("JasperReportsCGI.class").getPath());
	        //System.out.println("filePath: " + filePath + " :filePath");
	        //if (true)
	        //	return;

			HashMap<String, Object> parameters = new HashMap<String, Object>();
			parameters.put("ApplicationNumber", applicationNumber);

			JasperDesign design = JRXmlLoader.load(filePath);

			if (renderAs.equals("pdf")) {
				parameters.put("KeyFieldValue", keyFieldValue);
				parameters.put("ReportName", rName);
				JDesignerExtension jd = new JDesignerExtension(conn);
				// jd.addParameters(parameters);

				//jd.addImages(design, parameters, request);
				jd.addImages(design, parameters, server_name);
			}

			//System.out.println("Content-Type: text/html\n\n");
	        //System.out.println("+++++++++++++++++++++ OK +++++++++++++++++++");
	        //if (true)
	        //	return;

			
			JasperReport report = JasperCompileManager.compileReport(design);
			
	        //System.out.println("*************** JasperCompileManager: OK ****************");
			
			//System.out.println("Content-Type: text/html\n\n");
	        //System.out.println("report: " + report + " :report");
	        //if (true)
	        //	return;

			Locale locale = new Locale("ar", "KW");
			//Locale locale = new Locale("en", "US");
			parameters.put(JRParameter.REPORT_LOCALE, locale);

			// parameters.put(JRParameter.REPORT_LOCALE, Locale.AR);
			// Fill compiled JRXML file with data
			//JasperPrint print = wrapper.fillReport(report, parameters,	wrapper.getConnection());

			//if (true)
			//	throw new Exception("************* Ok ******************");
			
			//JasperPrint print = wrapper.fillReport(report, parameters, conn);
			JasperPrint print = wrapper.fillReport(report, parameters);

	        //System.out.println("*************** fillReport: OK ****************");
			
			//System.out.println("Content-Type: text/html\n\n");
	        //System.out.println("print: " + print);
	        //if (true)
	        //	return;			
			
			//if (true)
			//if (print == null)
			//	throw new Exception("print == null");
			//else
			//	throw new Exception("print != null");
			
			if (renderAs.equals("png")) {
				//response.setContentType("image/png");
				//System.out.println("Content-Type: image/png\n\n");
				
				//System.out.println("Content-Type: text/html\n\n");
		        //System.out.println("renderAs: " + renderAs);
				
		        //if (true)
		        //	return;
				
				//System.out.println("Content-Type: text/plain\n\n");
				//System.out.println("renderAs: " + renderAs);
				//if (true)
				//	return;
				
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

				//OutputStream out = response.getOutputStream();
				//exporter.setParameter(JRExporterParameter.OUTPUT_STREAM, out);
				exporter.setParameter(JRExporterParameter.OUTPUT_STREAM, System.out);

				// OutputStream out = new FileOutputStream("kuku-test.jpg");
				// exporter.setParameter(JRExporterParameter.OUTPUT_FILE, out);

				exporter.exportReport();

				//LOG.info("**** PNG ****");
				//if (true)
				//	throw new Exception("kuku");

				//LOG.info("**** PNG2 ****");
				
				//ImageIO.write((BufferedImage) image, "png", out);
				//System.out.flush();
				System.out.println("Content-Type: image/png\n");	// error, the second \n create an empty row in at the beginning of PNG file
				ImageIO.write((BufferedImage) image, "png", System.out);
				//System.out.close();
			} else {
				//response.setContentType("application/pdf");
				//System.out.println("Content-Type: text/html\n\n");
		        //System.out.println("renderAs: " + renderAs);

		        //System.out.println("Content-Type: application/pdf\n\n");

				//System.out.println("Content-Type: text/html\n\n");
		        //System.out.println("renderAs: " + renderAs);
		        //if (true)
		        //	return;
				
				// Export PDF file to browser window
				JRPdfExporter exporter = new JRPdfExporter();
				
				exporter.setParameter(JRExporterParameter.CHARACTER_ENCODING,
						"UTF-8");
				exporter.setParameter(JRExporterParameter.JASPER_PRINT, print);
				//exporter.setParameter(JRExporterParameter.OUTPUT_STREAM,
				//		response.getOutputStream());
				exporter.setParameter(JRExporterParameter.OUTPUT_STREAM,
						System.out);

				//System.out.flush();
		        System.out.println("Content-Type: application/pdf\n");
				exporter.exportReport();
				
				//LOG.info("**** PDF ****");

				//System.out.close();
				
				//LOG.info("**** 55 ****");
				
				
				
		        //if (true)
		        //	return;

				
				//System.out.println("Content-Type: text/html\n\n");
		        //System.out.println("exporter: " + exporter);
		        //if (true)
		        //	return;
				
			}			
		} catch (Exception e) {
			System.out.println("Content-Type: text/html\n\n");
			e.printStackTrace(System.out);
		}
	}
}
