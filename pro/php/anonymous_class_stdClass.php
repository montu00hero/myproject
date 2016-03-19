<?php

echo "stdClass is PHP's generic empty class, kind of like Object in Java or object in Python"
. " (Edit: but not actually used as universal base class; thanks @Ciaran for pointing this out). "
        . "It is useful for anonymous objects, dynamic properties, etc.";

echo'<br>';
echo'<br>';
echo'<br>';

echo"\$page=new stdClass();</br>
\$page->name='Home';</br>
\$page->status=1;</br>
</br>
</br>
class PageShow { </br>
                   </br>
    public \$currentpage;</br>
                           </br>
    public function __construct(\$pageobj)</br>
    {</br>
        \$this->currentpage = \$pageobj;</br>
    }</br>
</br>
    public function show()</br>
    {</br>
        echo \$this->currentpage->name;</br>
        \$state = (\$this->currentpage->status == 1) ? 'Active' : 'Inactive';</br>
        echo 'This is ' . \$state . ' page';</br>
    }</br>
}</br>
</br>
</br>
\$pageview=new PageShow(\$page);</br>
\$pageview->show();</br>";

 echo"<br> Output of above program:"; 



$page=new stdClass();
$page->name='Home';
$page->status=1;


class PageShow {

    public $currentpage;

    public function __construct($pageobj)
    {
        $this->currentpage = $pageobj;
    }

    public function show()
    {
        echo $this->currentpage->name;
        $state = ($this->currentpage->status == 1) ? 'Active' : 'Inactive';
        echo 'This is ' . $state . ' page';
    }
}


$pageview=new PageShow($page);
$pageview->show();