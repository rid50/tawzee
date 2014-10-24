using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Diagnostics;
using System.IO;

namespace BatchFileExecution
{
    public partial class WebForm1 : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            //tawzee:8084/TawzeeJasperReports/JasperServlet?reportName=TawzeeApplicationForm&applicationNumber=12345&keyFieldValue=12345&renderAs=png
            //string filepath = Server.MapPath(@"C:\tawzee\jetty\webapps\TawzeeJasperReports\WEB-INF\cgi-bin\RunJettyEmbedded.bat");
            //string filepath = @"C:\tawzee\jetty\webapps\TawzeeJasperReports\WEB-INF\cgi-bin\RunJettyEmbedded.bat";
            string file = @"C:\tawzee\jetty\webapps\TawzeeJasperReports\WEB-INF\cgi-bin\RunJasperReportsCGI.bat";

/*
            System.Collections.Specialized.NameValueCollection collection = Request.QueryString;
            String[] keyArray = collection.AllKeys;
            Response.Write("Keys:");
            foreach (string key in keyArray)
            {
                Response.Write("" + key + ": ");
                String[] valuesArray = collection.GetValues(key);
                foreach (string myvalue in valuesArray)
                {
                    Response.Write("\"" + myvalue + "\" ");
                }
            }
*/
            //Response.Write(Request.ServerVariables["QUERY_STRING"]);

            //ProcessStartInfo psi = new ProcessStartInfo("cmd.exe", "/c " + file);
            //ProcessStartInfo psi = new ProcessStartInfo("cmd.exe");
            ProcessStartInfo psi = new ProcessStartInfo();
            psi.FileName = file;
            //psi.WorkingDirectory = @"c:\tawzee\jetty\webapps\TawzeeJasperReports\WEB-INF\classes";
            psi.Arguments = "\"" + Request.ServerVariables["SERVER_NAME"] + "\" \"" + Request.ServerVariables["QUERY_STRING"] + "\"";
            psi.Arguments = "\"tawzee\" \"" + Request.ServerVariables["QUERY_STRING"] + "\"";
            psi.CreateNoWindow = false;
            psi.UseShellExecute = false;
            psi.RedirectStandardOutput = true;
            psi.RedirectStandardInput = true;
            psi.RedirectStandardError = true;
            Process proc = Process.Start(psi);

            proc.WaitForExit();

            // *** Read the streams ***
            System.IO.StreamReader sOut = proc.StandardOutput;
            System.IO.StreamReader sErr = proc.StandardError;

            int exitCode = proc.ExitCode;

            proc.Close();

            string output = sOut.ReadToEnd().Trim();
            string error = sErr.ReadToEnd().Trim();

            sOut.Close();
            sErr.Close();

            // Write out the results.
            string fmtStdOut = "<font face=courier size=0>{0}</font>";
            if (!String.IsNullOrEmpty(output))
                this.Response.Write(String.Format(fmtStdOut, "Output>> " + output.Replace(System.Environment.NewLine, "<br/>")));

            this.Response.Write("<br/><br/>");

            if (!String.IsNullOrEmpty(error))
            {
                this.Response.Write(String.Format(fmtStdOut, "Error>> " + error.Replace(System.Environment.NewLine, "<br/>")));
                this.Response.Write("<br/><br/>");
            }

            this.Response.Write(String.Format(fmtStdOut, "ExitCode: " + exitCode.ToString()));

/*
            StreamReader sr = File.OpenText(filepath);
            //StreamWriter sw = proc.StandardInput;

            // Attach the output for reading
            System.IO.StreamReader sOut = proc.StandardOutput;
            // Attach the in for writing
            System.IO.StreamWriter sIn = proc.StandardInput;

            while (sr.Peek() != -1)
            {
                // Make sure to add Environment.NewLine for carriage return!
                sIn.WriteLine(sr.ReadLine() + Environment.NewLine);
            }

            sr.Close();

            // Exit CMD.EXE
            string stEchoFmt = "# {0} run successfully. Exiting";

            sIn.WriteLine(String.Format(stEchoFmt, filepath));
            sIn.WriteLine("EXIT");

            // Close the process
            proc.Close();


            // Read the sOut to a string.
            string results = sOut.ReadToEnd().Trim();
            // Close the io Streams;
            sIn.Close(); 
            sOut.Close();

            // Write out the results.
            string fmtStdOut = "<font face=courier size=0>{0}</font>";
            this.Response.Write(String.Format(fmtStdOut, results.Replace(System.Environment.NewLine, "<br>")));
*/
        }
    }
}