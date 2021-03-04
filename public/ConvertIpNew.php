<?php
namespace checkIp\ConvertIpNew;

class ipAddress {
        private $fp;
        private $firstip;
        private $lastip;
        private $totalip;

        public function __construct($filename="qqwry.dat"){
            $this->fp=0;
            if(($this->fp=@fopen($filename,"rb"))!==false){
                $this->firstip=$this->getlong();
                $this->lastip=$this->getlong();
                $this->totalip=($this->lastip-$this->firstip)/7;
                register_shutdown_function(array(&$this,"__destruct"));
            }
        }

        public function __destruct(){
            if($this->fp){
                @fclose($this->fp);
            }
            $this->fp=0;
        }

        private function getlong(){
            $result=unpack("Vlong",fread($this->fp,4));
            return $result["long"];
        }

        private function getlong3(){
            $result=unpack("Vlong",fread($this->fp,3).chr(0));
            return $result["long"];
        }

        private function packip($ip){
            return pack("N",intval(ip2long($ip)));
        }

        private function getstring($data=""){
            $char=fread($this->fp,1);
            while(ord($char)>0){
                $data.=$char;
                $char=fread($this->fp,1);
            }
            return $data;
        }

        private function getarea(){
            $byte=fread($this->fp,1);
            switch(ord($byte)){
            case 0:
                $operators="";
            break;
            case 1:
            case 2:
                fseek($this->fp,$this->getlong3());
                $operators=$this->getstring();
            break;
            default:
                $operators=$this->getstring($byte);
            break;}
            return $operators;
        }

        public function getlocation($ip){
            if(!$this->fp){return null;}
            $location["ip"]=gethostbyname($ip);
            $ip=$this->packip($location["ip"]);
            $l=0;
            $u=$this->totalip;
            $findip=$this->lastip;
            while($l<=$u){
                $i=floor(($l+$u)/2);
                fseek($this->fp,$this->firstip+$i*7);
                $startip=strrev(fread($this->fp,4));
                if($ip<$startip){
                    $u=$i-1;
                }else{
                    fseek($this->fp,$this->getlong3());
                    $endip=strrev(fread($this->fp,4));
                    if($ip>$endip){
                        $l=$i+1;
                    }else{
                        $findip=$this->firstip+$i*7;
                        break;
                    }
                }
            }
            fseek($this->fp,$findip);
            $location["startip"]=long2ip($this->getlong());
            $offset=$this->getlong3();
            fseek($this->fp,$offset);
            $location["endip"]=long2ip($this->getlong());
            $byte=fread($this->fp,1);
            switch(ord($byte)){
            case 1:
                $countryOffset=$this->getlong3();
                fseek($this->fp,$countryOffset);
                $byte=fread($this->fp,1);
                switch(ord($byte)){
                case 2:
                    fseek($this->fp,$this->getlong3());
                    $location["area"]=$this->getstring();
                    fseek($this->fp,$countryOffset+4);
                    $location["operators"]=$this->getarea();
                break;
                default:
                    $location["area"]=$this->getstring($byte);
                    $location["operators"]=$this->getarea();
                break;}
            break;
            case 2:
                fseek($this->fp,$this->getlong3());
                $location["area"]=$this->getstring();
                fseek($this->fp,$offset+8);
                $location["operators"]=$this->getarea();
            break;
            default:
                $location["area"]=$this->getstring($byte);
                $location["operators"]=$this->getarea();
            break;}
            if($location["area"]=="CZ88.NET"){
                $location["area"]="未知";
            }
            if($location["operators"]=="CZ88.NET"){
                $location["operators"]="未知";
            }
            return $location;
        }
    }

