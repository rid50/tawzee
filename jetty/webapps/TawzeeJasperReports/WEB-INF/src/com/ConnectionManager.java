package com;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.util.Properties;

import javax.servlet.ServletException;

import org.apache.log4j.Logger;

public class ConnectionManager
{
	//private static final Logger LOG = Logger.getLogger(ConnectionManager.class);	
    Connection connection = null;

    public ConnectionManager() throws ServletException {
        Properties props = new Properties();
        InputStream is = null;

        try
        {
        	
        	//File file = new File("database.properties");
        	//System.out.println(file.getCanonicalPath());

            //fis = new FileInputStream("database.properties");
            //props.load(fis);
        	
          	is = ConnectionManager.class.getClassLoader().getResourceAsStream("database.properties");
          	if (is == null) {
				throw new Exception("Can't find 'database.properties' file");
          		
        		//System.out.println("Content-Type: text/html\n\n");
	            //System.out.println("Can't find 'database.properties' file");
	            //return;
          	}

    		//load a properties file from class path
    		props.load(is);

            Class.forName(props.getProperty("DB_DRIVER_CLASS"));
            
            connection = DriverManager.getConnection(props.getProperty("DB_URL"), 
            		props.getProperty("DB_USERNAME"), 
            		props.getProperty("DB_PASSWORD"));

            //System.out.println("connection: " + connection);
            
            
        } catch (Exception e) {

            //System.out.println("connection error: " + e.toString());
            //if (true)
            //	return;
			throw new ServletException(e.toString());
        	
			//System.out.println("Content-Type: text/html\n\n");
			//e.printStackTrace(System.out);
        } finally {
			if (is != null) {
				try {
					is.close();
				} catch (IOException e) {
					throw new ServletException(e.toString());
					
					//System.out.println("Content-Type: text/html\n\n");
					//e.printStackTrace(System.out);
				}
			}
        }
/*
        if (connection == null) {
			throw new Exception("Connection failed!");
        	
			//System.out.println("Content-Type: text/html\n\n");
            //System.out.println("Connection failed!");
        }
*/        
    }

    public Connection getConnection() {
    	return connection;
    }
    
    public void close() throws ServletException
    {
        try
        {
            connection.close();
        } catch (Exception e) {
			throw new ServletException(e.toString());
        	
			//System.out.println("Content-Type: text/html\n\n");
			//e.printStackTrace(System.out);
        }
    }

}

