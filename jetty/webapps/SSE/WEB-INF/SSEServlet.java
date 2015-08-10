import java.io.IOException;
import java.util.Date;
import java.util.Enumeration;

import javax.servlet.annotation.WebInitParam;
import javax.servlet.annotation.WebServlet;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.eclipse.jetty.servlets.EventSource;
import org.eclipse.jetty.servlets.EventSourceServlet;

import org.eclipse.jetty.util.log.Log;

@WebServlet(urlPatterns = "/sse", initParams = { @WebInitParam(name = "heartBeatPeriod", value = "5") }, asyncSupported = true)
public class SSEServlet extends EventSourceServlet {
	private static final long serialVersionUID = 1L;

	public void doGet(HttpServletRequest request, HttpServletResponse response)	throws ServletException, IOException
    {
		boolean isEvenStream = false;
		
        @SuppressWarnings("unchecked")
        Enumeration<String> acceptValues = request.getHeaders("Accept");
        while (acceptValues.hasMoreElements())
        {
            String accept = acceptValues.nextElement();

			Log.getRootLogger().info("accept: " + accept);			
            
			if (accept.equals("text/event-stream"))
            {
				isEvenStream = true;
				break;
            }
        }
		
		if (!isEvenStream)
		{
			try {
				if (request.getParameter("CheckConnection") != null) {
		Log.getRootLogger().info("Connection: " + request.getParameter("CheckConnection"));			
	
					response.addHeader("Access-Control-Allow-Origin", "*");
					response.addHeader("Access-Control-Allow-Methods",
							"POST, GET, OPTIONS, PUT, DELETE, HEAD");
					response.addHeader("Access-Control-Allow-Headers",
							"X-PINGOTHER, Origin, X-Requested-With, Content-Type, Accept");
					response.addHeader("Access-Control-Max-Age", "1728000");
					return;
				}
			} catch (Exception e) {
				//LOG.info("Exception: " + e.getMessage());
				//response.setContentType("text/html");
				//PrintWriter out = response.getWriter();
				//out.print(e.toString());
				
				//e.printStackTrace(System.out);
			}
			return;
		}
		
        super.doGet(request, response);
	}
	
	@Override
	protected EventSource newEventSource(final HttpServletRequest req) {
		return new EventSource() {

			@Override
			public void onOpen(final Emitter emitter) throws IOException {
				emitter.data("new server event " + new Date().toString());
				while (true) {
					System.out.println("propagating event..");
					try {
						Thread.sleep(5000);
						emitter.data("new server event " + new Date().toString());
					} catch (InterruptedException e) {
						e.printStackTrace();
					}
				}
			}

			@Override
			public void onClose() {
				System.out.println("closed");
			}
		};
	}
}
