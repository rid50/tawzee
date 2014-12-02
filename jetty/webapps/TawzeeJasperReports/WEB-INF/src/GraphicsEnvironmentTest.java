//import java.awt.AWTError;
import java.awt.GraphicsEnvironment;

public class GraphicsEnvironmentTest {
	public static void main(String[] args) {
		try
        {     
	        System.out.println("Java Class Path: " + System.getProperty("java.class.path"));
			System.out.println("<br/>");
			
			GraphicsEnvironment e = GraphicsEnvironment.getLocalGraphicsEnvironment();
			String[] fontnames = e.getAvailableFontFamilyNames();
			System.out.println("\nFonts available on this platform: ");
			for (int i = 0; i < fontnames.length; i++) {
				System.out.println(fontnames[i]);
				System.out.println("<br/>");
			}
        } catch (Throwable e)
        {     
			e.printStackTrace(System.out);
            //System.out.println("Not Ok");
        }     
	}
}
