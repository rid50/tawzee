   /*
 * Copyright (c) 2008, 2010, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 *
 * This code is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License version 2 only, as
 * published by the Free Software Foundation.  Oracle designates this
 * particular file as subject to the "Classpath" exception as provided
 * by Oracle in the LICENSE file that accompanied this code.
 *
 * This code is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
 * version 2 for more details (a copy is included in the LICENSE file that
 * accompanied this code).
 *
 * You should have received a copy of the GNU General Public License version
 * 2 along with this work; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * Please contact Oracle, 500 Oracle Parkway, Redwood Shores, CA 94065 USA
 * or visit www.oracle.com if you need additional information or have any
 * questions.
 */

package sun.font;

import java.awt.AWTError;
import java.awt.Font;
import java.awt.GraphicsEnvironment;
import java.awt.Toolkit;
import java.security.AccessController;
import java.security.PrivilegedAction;

import sun.security.action.GetPropertyAction;


/**
 * Factory class used to retrieve a valid FontManager instance for the current
 * platform.
 *
 * A default implementation is given for Linux, Solaris and Windows.
 * You can alter the behaviour of the {@link #getInstance()} method by setting
 * the {@code sun.font.fontmanager} property. For example:
 * {@code sun.font.fontmanager=sun.awt.X11FontManager}
 */
public final class FontManagerFactory {

    /** Our singleton instance. */
    private static FontManager instance = null;

    private static final String DEFAULT_CLASS;
    static {
        //System.out.println("############ FontManagerFactory00 #########: ");        
        if (FontUtilities.isWindows)
            DEFAULT_CLASS = "sun.awt.Win32FontManager";
        else
            DEFAULT_CLASS = "sun.awt.X11FontManager";
    }

    /**
     * Get a valid FontManager implementation for the current platform.
     *
     * @return a valid FontManager instance for the current platform
     */
    public static synchronized FontManager getInstance() {

        //System.out.println("############ FontManagerFactory0 #########: " + instance);        
/*
    	String path = System.getProperty("gnu.classpath.boot.library.path");
        path = path.substring(0, path.lastIndexOf('/', path.length()));
    	System.setProperty("java.library.path", path + "/amd64");
    	System.setProperty("java.library.path", "/var/www/java-se-7-ri/jre/lib/amd64");    	
*/
    	System.out.println("<br/>java.class.path: " + System.getProperty("java.class.path") + "<br/>");
        System.out.println("java.library.path: " + System.getProperty("java.library.path") + "<br/>");
        System.out.println("sun.boot.class.path: " + System.getProperty("sun.boot.class.path") + "<br/>");
        System.out.println("gnu.classpath.boot.library.path: " + System.getProperty("gnu.classpath.boot.library.path") + "<br/>");
    	
        if (instance != null) {
            return instance;
        }

        //System.out.println("############ FontManagerFactory #########: " + DEFAULT_CLASS);        
        AccessController.doPrivileged(new PrivilegedAction() {

            public Object run() {
                try {
                    String fmClassName =
                            System.getProperty("sun.font.fontmanager",
                                               DEFAULT_CLASS);
                    System.out.println("############ ClassName #########: " + fmClassName);        

                    ClassLoader cl = ClassLoader.getSystemClassLoader();
                    System.out.println("############ ClassLoader #########: " + cl);        
                    Class fmClass = Class.forName(fmClassName, true, cl);
                    System.out.println("############ Class #########: " + fmClass);        
                    instance = (FontManager) fmClass.newInstance();
                } catch (ClassNotFoundException ex) {
                    System.out.println("!!!!!!!!!!!!! ClassNotFoundException !!!!!!!!!: " + ex.toString());        
                    InternalError err = new InternalError();
                    err.initCause(ex);
                    throw err;

                } catch (InstantiationException ex) {
                    System.out.println("!!!!!!!!!!!!! InstantiationException !!!!!!!!!: " + ex.toString());        
                    InternalError err = new InternalError();
                    err.initCause(ex);
                    throw err;

                } catch (IllegalAccessException ex) {
                    System.out.println("!!!!!!!!!!!!! IllegalAccessException !!!!!!!!!: " + ex.toString());        
                    InternalError err = new InternalError();
                    err.initCause(ex);
                    throw err;
                } catch (Exception ex) {
                    System.out.println("!!!!!!!!!!!!! Exception !!!!!!!!!: " + ex.toString());        
                    InternalError err = new InternalError();
                    err.initCause(ex);
                    throw err;
                } catch (Throwable ex) {
                    System.out.println("!!!!!!!!!!!!! Throwable !!!!!!!!!: " + ex.toString());        
                    InternalError err = new InternalError();
                    err.initCause(ex);
                    throw err;
                }
                return null;
            }
        });
        System.out.println("############ Instance #########: " + instance);        

        return instance;
    }
}
