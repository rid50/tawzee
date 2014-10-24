import java.awt.Color;
import java.awt.Graphics2D;
import java.awt.Image;
import java.awt.RenderingHints;
import java.awt.image.BufferedImage;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.sql.Connection;
import java.util.HashMap;
import java.util.Locale;

import javax.imageio.ImageIO;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import net.sf.jasperreports.engine.JRExporterParameter;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperCompileManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.JasperPrintManager;
import net.sf.jasperreports.engine.JasperReport;
import net.sf.jasperreports.engine.design.JasperDesign;
import net.sf.jasperreports.engine.export.JRGraphics2DExporter;
import net.sf.jasperreports.engine.export.JRGraphics2DExporterParameter;
import net.sf.jasperreports.engine.export.JRPdfExporter;
import net.sf.jasperreports.engine.xml.JRXmlLoader;

import com.JDesignerExtension;
import com.JasperReportsWrapper;

public class JasperServlet extends HttpServlet {
	private static final long serialVersionUID = 4350549139109004305L;

	public void doGet(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException {
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

			// Connect to DB and compile JRXML file
			JasperReportsWrapper wrapper = new JasperReportsWrapper();
			Connection conn = wrapper.getConnection();
			
	        if (conn == null) {
	            System.out.println("Connection failed!");
	            return;
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
			JasperPrint print = wrapper.fillReport(report, parameters,
					wrapper.getConnection());

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

				OutputStream out = response.getOutputStream();
				exporter.setParameter(JRExporterParameter.OUTPUT_STREAM, out);

				// OutputStream out = new FileOutputStream("kuku-test.jpg");
				// exporter.setParameter(JRExporterParameter.OUTPUT_FILE, out);

				exporter.exportReport();

				ImageIO.write((BufferedImage) image, "png", out);
				out.close();
			} else {
				response.setContentType("application/pdf");

				// Export PDF file to browser window
				JRPdfExporter exporter = new JRPdfExporter();
				exporter.setParameter(JRExporterParameter.CHARACTER_ENCODING,
						"UTF-8");
				exporter.setParameter(JRExporterParameter.JASPER_PRINT, print);
				exporter.setParameter(JRExporterParameter.OUTPUT_STREAM,
						response.getOutputStream());
				exporter.exportReport();
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
}
