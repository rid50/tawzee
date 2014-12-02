package com;

import java.io.File;
import java.sql.Connection;
import java.util.HashMap;
import java.util.Map;

import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JasperCompileManager;
import net.sf.jasperreports.engine.JasperExportManager;
import net.sf.jasperreports.engine.JasperFillManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.JasperReport;

public class JasperReportsWrapper
{
/*	
    public static String dbServerAdd = "tawzee.mew.gov.kw";
    //public static String dbServerAdd = "localhost";
    public static int dbServerPort = 3306;
    public static String dbName = "tawzee";
    //public static String dbUser = "romains";
    //public static String dbPass = "npX3ZNcu";
    public static String dbUser = "romanroot";
    public static String dbPass = "%@eSTxT~)g{$FaQ!!~";
*/

    private Connection connection = null;
    private ConnectionManager conManager = null;

    public JasperReportsWrapper ()   {}

    public Connection connect2DB ()
    {
        conManager = new ConnectionManager();
        connection = conManager.getConnection();
        return connection;
    }

    public Connection getConnection() {
        conManager = new ConnectionManager();
        connection = conManager.getConnection();
        return connection;
    }

    public JasperReport compileJRXMLFile(String jasperXMLFileName)
    {
    	JasperReport jr = null;
        try
        {
            jr = JasperCompileManager.compileReport(jasperXMLFileName);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return jr;
    }

	//@SuppressWarnings("unchecked")
    public JasperPrint fillReport(JasperReport jasperReport, HashMap<String, Object>params, Connection conn) throws Exception
    {
        //if (true)
        //	throw new Exception("ReportWrapper");

		return JasperFillManager.fillReport(jasperReport, params, conn);
    }

    public void saveReportInPDF (JasperPrint jPrint, String pdfFileName) throws Exception
    {
        JasperExportManager.exportReportToPdfFile(jPrint, pdfFileName);
    }
/*
    public static void main(String[] args)
    {
        File file = new File("TawzeeReport.jrxml");
        String path2JRXMLFile = file.getAbsolutePath();
        String pdfFileName = "TawzeeReport.pdf";
    	
        JasperReportsWrapper wrapper = new JasperReportsWrapper();
        //Connection connection = wrapper.connect2DB(dbServerAdd, dbServerPort, dbName, dbUser, dbPass);
        Connection connection = wrapper.getConnection();
        JasperReport jasperReport = wrapper.compileJRXMLFile (path2JRXMLFile);
        JasperPrint jasperPrint = wrapper.fillReport(jasperReport, null, connection);
        wrapper.saveReportInPDF(jasperPrint, pdfFileName);
    }//main
*/
}
