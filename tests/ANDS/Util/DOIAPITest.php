<?php

namespace ANDS\Util;

use PHPUnit\Framework\TestCase;

class DOIAPITest extends TestCase
{

    public function testResolve()
    {
        $dataciteDOI = "10.22004/ag.econ.295222";
        $crossrefDOI = "10.23943/princeton/9780691143972.003.0001";

        $result = DOIAPI::resolve($dataciteDOI);
        $this->assertNotNull($result);
        $this->assertEquals("Plants, Handlers, and Bulk Tank Units Under the New York-New Jersey Marketing Orders", $result['title']);

        $result = DOIAPI::resolve($crossrefDOI);
        $this->assertNotNull($result);
        $this->assertNotEmpty($result['source']);
    }

    public function testResolveDOIContentNegotiation()
    {
        $dataciteDOI = "10.22004/ag.econ.295222";
        $crossrefDOI = "10.23943/princeton/9780691143972.003.0001";

        $result = DOIAPI::resolveDOIContentNegotiation($dataciteDOI);
        $this->assertNotNull($result, "DataCite DOI is resolvable via Content Negotiation");

        $result = DOIAPI::resolveDOIContentNegotiation($crossrefDOI);
        $this->assertNotNull($result, "CrossRef DOI is resolvable via Content Negotiation");
    }
}
