set CLASSPATH=".;WEB-INF\lib\servlet-api-3.1.jar;WEB-INF\lib\jetty-servlets-7.0.0.RC4.jar;WEB-INF\lib\jasperreports-5.5.1.jar;WEB-INF\classes;"
javac WEB-INF\src\JasperServletCGI.java -Xlint:unchecked -Xlint:deprecation -d WEB-INF\classes
@rem javac WEB-INF\src\JasperServlet.java -Xlint:unchecked -Xlint:deprecation -d WEB-INF\classes

:: set classpath=WEB-INF\lib\jasperreports-5.5.1.jar
:: javac WEB-INF\classes\*.java -Xlint:unchecked -d WEB-INF\classes

