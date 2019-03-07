<?php 

namespace Netpower\Momo\Helper;

class CatchErrorCode 
{
    public function catchError($errorCode)
    {
        switch($errorCode)
        {
            case 3 : 
                return "Account Information doesn't exist"; 
                break;
            case 4 : 
                return "Haven't register service";
                break;
            case 7 : 
                return "You're not allowed access";
                break;
            case 8 :
                return "wrong password";
                break;
            case 9 : 
                return "Password expired";
                break;
            case 11 : 
                return "";
                break;
            case 13 : 
                return "";
                break;
            case 14 : 
                return "";
                break;
            case 17 : 
                return "";
                break;    
            case 23 : 
                return "";
                break;    
            case 27 : 
                return "";
                break;    
            case 32 : 
                return "";
                break;    
            case 45 : 
                return "";
                break;    
            case 46 : 
                return "";
                break;    
            case 47 : 
                return "";
                break;    
            case 48 : 
                return "";
                break;    
            case 103 : 
                return "";
                break;    
            case 151 : 
                return "";
                break;    
            case 153 : 
                return "";
                break;    
            case 161 : 
                return "";
                break;    
            case 162 : 
                return "";
                break;    
            case 204 : 
                return "";
                break;    
            case 208 : 
                return "";
                break;    
            case 210 : 
                return "";
                break;    
            case 403 : 
                return "";
                break;    
            case 404 : 
                return "";
                break;    
            case 1001 : 
                return "";
                break;    
            case 1002 : 
                return "";
                break;    
            case 1004 : 
                return "";
                break;    
            case 1006 : 
                return "";
                break;    
            case 1007 : 
                return "";
                break;    
            case 1013 : 
                return "";
                break;    
            case 1014 : 
                return "";
                break;    
            case 1020 : 
                return "";
                break;    
            case 2117 : 
                return "";
                break;    
            case 2119 : 
                return "";
                break;    
            case 2125 : 
                return "";
                break;    
            case 2126 : 
                return "";
                break;    
            case 2127 : 
                return "";
                break;    
            case 2128 : 
                return "";
                break;    
            case 2129 : 
                return "";
                break;    
            case 2131 : 
                return "";
                break;    
            case 2132 : 
                return "";
                break;    
            case 2133 : 
                return "";
                break;    
            case 2135 : 
                return "";
                break;    
            case 2140 : 
                return "";
                break;    
            case 2400 : 
                return "";
                break;    
            case 9000 : 
                return "";
                break;
            case 9003 : 
                return "";
                break;
            default : 
                return "Unknown Error";            
        }
    }
}