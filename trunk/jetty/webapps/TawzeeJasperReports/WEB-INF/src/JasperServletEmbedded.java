import org.eclipse.jetty.server.Server;
import org.eclipse.jetty.servlet.ServletContextHandler;
import org.eclipse.jetty.servlet.ServletHolder;
import org.eclipse.jetty.servlets.CGI;

public class JasperServletEmbedded {
	public static void main(String[] args) throws Exception {
		// The core Jetty server running on 8084
		Server server = new Server(8084);

		// Setup CGI routing
		ServletContextHandler context = new ServletContextHandler(ServletContextHandler.SESSIONS);
		context.setContextPath("/");
		//context2.setInitParameter("commandPrefix", "perl");

		// Where does this point to? 
		//context2.setInitParameter("cgibinResourceBase", "cgi-bin");
		server.setHandler(context);

		// Add the CGI Servlet
		//context2.addServlet(new ServletHolder(new CGI()), "/cgi");
		context.addServlet(new ServletHolder(new JasperServlet()), "/JasperServlet");

		server.start();
		server.join();
	}
}
