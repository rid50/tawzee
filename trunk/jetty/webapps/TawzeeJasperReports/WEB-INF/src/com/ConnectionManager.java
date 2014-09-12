package com;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.util.Properties;

public class ConnectionManager
{
    Connection connection = null;

    public ConnectionManager () {
    	
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
	            System.out.println("Can't find 'database.properties' file");
	            return;
          	}

    		//load a properties file from class path
    		props.load(is);

            Class.forName(props.getProperty("DB_DRIVER_CLASS"));
            
            connection = DriverManager.getConnection(props.getProperty("DB_URL"), 
            		props.getProperty("DB_USERNAME"), 
            		props.getProperty("DB_PASSWORD"));
            
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
			if (is != null) {
				try {
					is.close();
				} catch (IOException e) {
					e.printStackTrace();
				}
			}
        }

        if (connection == null)
            System.out.println("Connection failed!");
    }

    public Connection getConnection() {
    	return connection;
    }
    
    public void close()
    {
        try
        {
            connection.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

}
