import java.io.IOException;
import java.util.Locale;
import java.util.Date;
import java.util.Enumeration;
import java.util.Set;
import java.util.concurrent.CopyOnWriteArraySet;
import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.TimeUnit;

//import java.text.DateFormat;
//import java.text.SimpleDateFormat;

//import java.security.MessageDigest;

import javax.servlet.annotation.WebInitParam;
import javax.servlet.annotation.WebServlet;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.eclipse.jetty.servlets.EventSource;
import org.eclipse.jetty.servlets.EventSource.Emitter;
import org.eclipse.jetty.servlets.EventSourceServlet;

import org.eclipse.jetty.util.log.Log;

@WebServlet(urlPatterns = "/sse", initParams = { @WebInitParam(name = "heartBeatPeriod", value = "5") }, asyncSupported = true)
public class SSEServlet extends EventSourceServlet {
	private static final long serialVersionUID = 1L;
	private String _data = "";
	private final Set<Emitter> emitters = new CopyOnWriteArraySet<>();
	 
   private volatile ScheduledExecutorService executor;
    
    @Override
    public void init() throws ServletException
    {
        super.init();
        this.executor = Executors.newSingleThreadScheduledExecutor();
        this.executor.scheduleAtFixedRate(new SendComment(), 0, 10, TimeUnit.SECONDS);
    }
    
    @Override
    public void destroy()
    {
        this.executor.shutdown();
        this.emitters.clear();
        super.destroy();
    }
	
	public void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
    {
		for (Emitter emitter : emitters)
		{
			try
			{
				emitter.data(request.getParameter("data"));
			}
			catch (IOException e)
			{
				Log.getRootLogger().info("cannot emit an update: " + e);			
			}
		}
	}
	
	public void doGet(HttpServletRequest request, HttpServletResponse response)	throws ServletException, IOException
    {
		boolean isEvenStream = false;

		response.setHeader("Access-Control-Allow-Origin", "*");
		//response.setHeader("Access-Control-Allow-Methods", "POST, GET, OPTIONS, PUT, DELETE, HEAD");
		//response.setHeader("Access-Control-Allow-Headers", "X-PINGOTHER, Origin, X-Requested-With, Content-Type, Accept");
		//response.setHeader("Access-Control-Max-Age", "1728000");
		//response.setHeader("Access-Control-Expose-Headers", "*");
		//response.setHeader("Access-Control-Allow-Credentials", "true");

        @SuppressWarnings("unchecked")
        Enumeration<String> acceptValues = request.getHeaders("Accept");
        while (acceptValues.hasMoreElements())
        {
            String accept = acceptValues.nextElement();
			if (accept.equals("text/event-stream"))
            {
				isEvenStream = true;
				break;
            }
        }
		
		if (!isEvenStream)	// CheckConnection
			return;

		super.doGet(request, response);
	}
	
    final class SendComment implements Runnable {
        //@Override
        public void run()
        {
            String serverTime = String.format("%tT", System.currentTimeMillis());
			//Log.getRootLogger().info("Time: " + serverTime);			

            for (Emitter emitter : emitters)
            {
                try
                {
                    emitter.comment(serverTime);
                }
                catch (IOException e)
                {
					Log.getRootLogger().info("cannot emit a comment: " + e);			
                }
            }
            
        }
    }
	
	@Override
	protected EventSource newEventSource(final HttpServletRequest request) {
		return new EventSource() {
			private Emitter emitter;
			private boolean closed = false;

			@Override
			public void onOpen(final Emitter emitter) throws IOException {
				this.emitter = emitter;
				emitters.add(emitter);

				//_emitter = emitter;
				//DateFormat df = new SimpleDateFormat("dd/MM/yyyy");
				//String dt = df.format(new Date()).toString();
				//String json = "{\"op\" : \"setOwnerSignature\", \"date\" : \"" + dt + "\"}";
				//emitter.data(json);
				
				//if (request.getParameter("DataPost") != null) {
				//	emitter.data(request.getParameter("data"));
				//}
/*				
				executor.schedule(new Runnable()
				{
					@Override
					public void run()
					{
						try
						{
							String serverTime = String.format("%tT", System.currentTimeMillis());
							//Log.getRootLogger().info("Time: " + serverTime);			
							//serverTime = String.format(Locale.US, "%tT", System.currentTimeMillis());
							//Log.getRootLogger().info("Time(US): " + serverTime);			
							//serverTime = String.format(Locale.FRANCE, "%tT", System.currentTimeMillis());
							Log.getRootLogger().info("Time(FRANCE): " + serverTime);			
							emitter.comment(serverTime);
						}
						catch (IOException e)
						{
							Log.getRootLogger().info("cannot emit a comment: " + e);			
						}
						
					}
				}, 10L, TimeUnit.SECONDS);
*/				
/*				
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
*/				
			}
       
/*			
			public void emitEvent(String dataToSend)
			{
				try {
					this.emitter.data(dataToSend);
				} catch (Exception e) {
					e.printStackTrace();
				}
			}
*/
			@Override
			public void onClose() {
	            closed = true;
				emitters.remove(this.emitter);
			}
		};
	}
}
