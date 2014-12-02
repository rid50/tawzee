@rem Dim WSHShell
@rem Set WSHShell = WScript.CreateObject("WScript.Shell")
@rem cmdStr="java "
@rem cmdStr=cmdStr&"-Dcgi.query_string=%QUERY_STRING% "
@rem cmdStr=cmdStr&"-Dcgi.server_name=%SERVER_NAME% "
@rem cmdStr=cmdStr&"CGIRequest "
@rem Return=WshShell.Run (cmdStr,0,TRUE) 

@rem CLASSPATH=".;WEB-INF\classes;c:\tawzee\jetty\lib\*;c:\tawzee\jetty\webapps\TawzeeJasperReports\WEB-INF\lib\*;"

@rem java -cp c:\tawzee\jetty\webapps\TawzeeJasperReports\WEB-INF\lib\*;c:\tawzee\jetty\webapps\TawzeeJasperReports\WEB-INF\classes; -Dcgi.server_name=%1 -Dcgi.query_string=%2 JasperReportsCGI
java -cp c:\tawzee\jetty\webapps\TawzeeJasperReports\WEB-INF\lib\*;c:\tawzee\jetty\webapps\TawzeeJasperReports\WEB-INF\classes; -Dcgi.server_name="tawzee.mew.gov.kw" -Dcgi.query_string="reportName=TawzeeApplicationForm&applicationNumber=2/12345&keyFieldValue=2/12345&renderAs=png" JasperReportsCGI

