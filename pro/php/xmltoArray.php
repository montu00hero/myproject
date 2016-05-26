<?php

class xml2array{



function xmlstr_to_array($xmlstr) {
        $doc = new DOMDocument();
        $doc->loadXML($xmlstr);
        return $this->domnode_to_array($doc->documentElement);
    }

    function domnode_to_array($node) {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domnode_to_array($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        if (!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } elseif ($v) {
                        $output = (string) $v;
                    }
                }
                if (is_array($output)) {
                    if ($node->attributes->length)
                     {
                        $a = array();
                        foreach ($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }

                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1 && $t != '@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }

        return $output;
    }  
    
}  
    
  $arr=new xml2array();

$xmlstr='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sec="http://xml.amadeus.com/2010/06/Security_v1" xmlns:typ="http://xml.amadeus.com/2010/06/Type">
<soapenv:Header>
	<awsse:Session TransactionStatusCode="InSeries" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3">
        <awsse:SessionId></awsse:SessionId>
		<awsse:SequenceNumber></awsse:SequenceNumber>
		<awsse:SecurityToken></awsse:SecurityToken>
       </awsse:Session>
       <add:MessageID xmlns:add="http://www.w3.org/2005/08/addressing">urn:uuid:0001-c0a80016-57469aff-69ad-7974995e</add:MessageID>
       <add:Action xmlns:add="http://www.w3.org/2005/08/addressing">http://webservices.amadeus.com/PNRXCL_14_1_1A</add:Action>
       <add:To xmlns:add="http://www.w3.org/2005/08/addressing">https://nodeD1.test.webservices.amadeus.com/1ASIWIBEMVL</add:To>
</soapenv:Header>
<soapenv:Body>
<PNR_Cancel xmlns="http://xml.amadeus.com/PNRXCL_14_1_1A">
    <reservationInfo>
        <reservation>
            <controlNumber>YMAGBS</controlNumber>
        </reservation>
    </reservationInfo>
    <pnrActions>
        <optionCode>10</optionCode>
    </pnrActions>
    <cancelElements>
        <entryType>I</entryType>
    </cancelElements>
</PNR_Cancel>
</soapenv:Body>
</soapenv:Envelope>';



  $dr= $arr->xmlstr_to_array($xmlstr);      
        

  echo"<pre>";print_r($dr);
  