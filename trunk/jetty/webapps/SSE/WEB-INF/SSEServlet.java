import java.io.IOException;
import java.util.Date;
import java.util.Enumeration;
//import java.util.Random;

import java.text.DateFormat;
import java.text.SimpleDateFormat;

import java.security.MessageDigest;

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
	private String _data = "";
	static private EventSource.Emitter _emitter;
	
	public void doGet(HttpServletRequest request, HttpServletResponse response)	throws ServletException, IOException
    {
		go(request, response);
	}
	
	public void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
    {
		go(request, response);
	}
	
	public void go(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
    {
		//boolean isEvenStream = false;

		response.setHeader("Access-Control-Allow-Origin", "*");
		//response.setHeader("Access-Control-Allow-Methods", "POST, GET, OPTIONS, PUT, DELETE, HEAD");
		//response.setHeader("Access-Control-Allow-Headers", "X-PINGOTHER, Origin, X-Requested-With, Content-Type, Accept");
		//response.setHeader("Access-Control-Max-Age", "1728000");
		//response.setHeader("Access-Control-Expose-Headers", "*");
		//response.setHeader("Access-Control-Allow-Credentials", "true");

		if (request.getParameter("CheckConnection") != null) {
			//Log.getRootLogger().info("Connection: " + request.getParameter("CheckConnection"));			
			return;
		} else if (request.getParameter("PostData") != null) {
			//Log.getRootLogger().info("data: " + request.getParameter("data"));			
			Log.getRootLogger().info("Emitter: " + _emitter);			
			if (_emitter != null)
				_emitter.data(request.getParameter("data"));

			//_data = request.getParameter("data");
			return;
		}
/*

		
        @SuppressWarnings("unchecked")
        Enumeration<String> acceptValues = request.getHeaders("Accept");
        while (acceptValues.hasMoreElements())
        {
            String accept = acceptValues.nextElement();

			//Log.getRootLogger().info("accept: " + accept);			
            
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
					//Log.getRootLogger().info("Connection: " + request.getParameter("CheckConnection"));			
					return;
				}
			} catch (Exception e) {
				//LOG.info("Exception: " + e.getMessage());
				//response.setContentType("text/html");
				//PrintWriter out = response.getWriter();
				//out.print(e.toString());
				
				//e.printStackTrace(System.out);
				return;
			}
		}
*/		
        super.doGet(request, response);
	}
	
	@Override
	protected EventSource newEventSource(final HttpServletRequest request) {
		return new EventSource() {

			@Override
			public void onOpen(final Emitter emitter) throws IOException {
				_emitter = emitter;
				//DateFormat df = new SimpleDateFormat("dd/MM/yyyy");
				//String dt = df.format(new Date()).toString();
				//String json = "{\"op\" : \"setOwnerSignature\", \"date\" : \"" + dt + "\"}";
				//emitter.data(json);
				
				//if (request.getParameter("DataPost") != null) {
				//	emitter.data(request.getParameter("data"));
				//}
				
				while (true) {
					//System.out.println("propagating event..");
					try {
						Thread.sleep(10000);
						double random = Math.random();
						MessageDigest crypt = MessageDigest.getInstance("SHA-1");
						crypt.reset();
						crypt.update(Double.toString(random).getBytes("UTF-8"));
						
						//String str = new java.math.BigInteger(1, crypt.digest()).toString(16);
						//Log.getRootLogger().info("accept: " + str);			

						//emitter.data("{: " + new java.math.BigInteger(1, crypt.digest()).toString(16) + "}\n\n");
						emitter.comment(new java.math.BigInteger(1, crypt.digest()).toString(16));
					} catch (Exception e) {
						e.printStackTrace();
					}
				}
			}

			@Override
			public void onClose() {
				//System.out.println("closed");
			}
		};
	}
}
