@rem set CLASSPATH=".;WEB-INF\lib\servlet-api-3.1.jar;WEB-INF\lib\jasperreports-5.5.1.jar;WEB-INF\classes;"
set CLASSPATH=..\..\..\lib\servlet-api-3.1.jar;..\..\..\lib\jetty-util-9.1.2.v20140210.jar;lib\jetty-eventsource-servlet-1.0.0.jar
javac SSEServlet.java -d classes

:: Linux machine
@rem cd /var/www/html/jetty/webapps/SSE/WEB-INF
@rem javac -cp ../../../lib/servlet-api-3.1.jar:lib/jetty-util-9.1.2.v20140210.jar:lib/jetty-eventsource-servlet-1.0.0.jar SSEServlet.java -d classes

@rem javac -cp /var/www/html/jetty/lib/servlet-api-3.1.jar:/var/www/html/jetty/webapps/SSE/WEB-INF/lib/jetty-util-9.1.2.v20140210.jar:/var/www/html/jetty/webapps/SSE/WEB-INF/lib/jetty-eventsource-servlet-1.0.0.jar SSEServlet.java -d classes

@rem javac WEB-INF\src\JasperServlet.java -Xlint:unchecked -Xlint:deprecation -d WEB-INF\classes

:: set classpath=WEB-INF\lib\jasperreports-5.5.1.jar
:: javac WEB-INF\classes\*.java -Xlint:unchecked -d WEB-INF\classes

