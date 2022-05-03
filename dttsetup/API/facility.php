<?php
use App\Plugins\Di\Injectable;

use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions as expt;

class facility extends Injectable{

    public function auth(){
        $sql = "INSERT INTO securitytokens(token,time) VALUES(?,?)";
        $token = bin2hex(random_bytes(16));
        $currtime = time() + 60*60;
        $convtime = date('Y-m-d H:i:s',$currtime);
        if($this->db->executeQuery($sql,[$token,$convtime]))
        {
            return (new Status\Ok(["token" => $token]))->send();
        }
        else{
            return (new Status\InternalServerError(["error" => "adding token to server"]))->send();   
        }
    }

    public function isauth(){
        $sql = "SELECT `time` FROM securitytokens WHERE token = ?";
        $headers = apache_request_headers();
        if($headers['Authorization'])
        {
            $token = str_replace("Bearer ","",$headers['Authorization']);
            if($this->db->executeQuery($sql,[$token]))
            {
                $res = $this->db->getStatement();
                if($res->rowCount() > 0)
                {
                    $timearray = $res->fetch(PDO::FETCH_ASSOC);
                    $tm = strtotime($timearray['time']);
                    $now = time();
                    $chkval = $tm - $now;
                    if($chkval > 0)
                    {
                        return true;
                    }
                    else{
                        return (new Status\Unauthorized(["error" => "expired token"]))->send();
                    }
                }
                else{
                    return (new Status\Unauthorized(["error" => "token not found in server"]))->send();
                }
            }else{
                return (new Status\Unauthorized(["error" => "adding token to server"]))->send();
            }
        }else{
            return (new Status\Unauthorized(["error" => "Authorization Token has not been passed"]))->send();
        }
    }

    public function getdetails(){
        // Reading only facilities and their tags
        $sql = "SELECT * FROM facility";
        $tagquery = "SELECT tagname from tag WHERE id in (SELECT tag_id from factagjun WHERE fac_id = ?)";
        $this->db->executeQuery($sql);
        $res = $this->db->getStatement();
        if($res->rowCount() > 0)
        {
            $facilarr = [];
            while($row = $res->fetch(PDO::FETCH_ASSOC))
            {
                extract($row);
                $this->db->executeQuery($tagquery,[$id]);
                $tgres = $this->db->getStatement();
                $tg = $tgres->fetchAll(PDO::FETCH_ASSOC);
                $tgarr = [];
                foreach($tg as $vl)
                {
                    array_push($tgarr,$vl['tagname']);
                }
                $er = array(
                    "id" => $id,
                    "Facility_Name" => $name,
                    "Date_of_Creation" => $doc,
                    "Tags" => $tgarr
                );
                array_push($facilarr,$er);
            }
            return (new Status\Ok($facilarr))->send();
        }
        else{
            return (new expt\NotFound(['204'=>'emptydata']))->send();
        }
    }

    public function addDetails($nm,$tags = [], $ct,$adr,$zipc,$cc,$pn){
        $dt = date("Y-m-d");
        $tagaddquery = "INSERT INTO tag (tagname)
                        Select ? Where not exists(select * from tag where tagname=?)";
        $tagidselect = "SELECT id FROM tag WHERE tagname IN ('".implode("','",$tags)."')";
        $faciquery = "INSERT INTO facility(name,doc) VALUES(?,?)";
        $locquery = "INSERT INTO location(fac_id, city, address, zipcode, countrycode, Phone)
                                VALUES(?,?,?,?,?,?)";
        $junc = "INSERT INTO factagjun(fac_id,tag_id)
                                VALUES(?,?)";
        $tagids = [];
        $fcid = -1;
        foreach($tags as $tg)
        {
            if(!($this->db->executeQuery($tagaddquery,[$tg,$tg])))
            {
                return (new Status\InternalServerError(["tag" => "failed to add"]))->send();
            }
        }
        if($this->db->executeQuery($tagidselect))
        {
            $res = $this->db->getStatement();
            $tagres = $res->fetchAll(PDO::FETCH_ASSOC);
            foreach($tagres as $tg)
            {
                array_push($tagids,$tg['id']);
            }
        }
        else{
            return (new Status\InternalServerError(["error" => "fetching ids"]))->send();
        }
        if($this->db->executeQuery($faciquery,[$nm,$dt]))
        {
            $this->db->executeQuery("SELECT LAST_INSERT_ID()");
            $fstmt = $this->db->getStatement();
            $fcid = $fstmt->fetchColumn();
            if($this->db->executeQuery($locquery,[$fcid,$ct,$adr,$zipc,$cc,$pn]))
            {
                foreach($tagids as $tg)
                {
                    if(!($this->db->executeQuery($junc,[$fcid,$tg])))
                    {
                        return (new Status\InternalServerError(["junction" => "failed to create junction"]))->send();
                    }
                }
            }else{
                return (new Status\InternalServerError(["location" => "failed to add location"]))->send();
            }
        }else{
            return (new Status\InternalServerError(["facility" => "failed to add facility"]))->send();
        }

        return (new Status\Created(["Success" => "Facility location and tags is successfully added"]))->send();

    }

    public function updetails($fid,$fname,$doc,$tgs = []){
        $facupqury = "UPDATE facility SET name=?, doc=? WHERE id = ?";
        $delejunc = "DELETE FROM factagjun WHERE fac_id = ?";
        $tagaddquery = "INSERT INTO tag (tagname)
                        Select ? Where not exists(select * from tag where tagname=?)";
        $tagidselect = "SELECT id FROM tag WHERE tagname IN ('".implode("','",$tgs)."')";

        $junc = "INSERT INTO factagjun(fac_id,tag_id)
                                VALUES(?,?)";

        $tagids = [];
        if($this->db->executeQuery($facupqury,[$fname,$doc,$fid]))
        {
            if(!(in_array("-",$tgs)))
            {
                $this->db->executeQuery($delejunc,[$fid]);
                foreach($tgs as $tg)
                {
                    if(!($this->db->executeQuery($tagaddquery,[$tg,$tg])))
                    {
                        return (new Status\InternalServerError(["Update error" => "Cannot Update tags"]))->send();
                    }
                }
                if($this->db->executeQuery($tagidselect))
                {
                    $res = $this->db->getStatement();
                    $tagres = $res->fetchAll(PDO::FETCH_ASSOC);
                    foreach($tagres as $tg)
                    {
                        array_push($tagids,$tg['id']);
                    }
                }else{
                    return (new Status\InternalServerError(["Update error" => "Cannot select updated ids"]))->send();
                }
                foreach($tagids as $tg)
                {
                    if(!($this->db->executeQuery($junc,[$fid,$tg])))
                    {
                        return (new Status\InternalServerError(["Update Error" => "failed to create updated junction"]))->send();
                    }
                }
                return (new Status\Ok(["Updated" => "Successfully updated facility and tags"]))->send();
            }
            else{
                return (new Status\Ok(["Updated" => "Successfully updated facility and no change in tags"]))->send();
            }
        }
    }

    public function deletefac($fid){
        $delqry = "DELETE FROM facility WHERE id = ?"; // The junction will be cascaded
        if($this->db->executeQuery($delqry,[$fid]))
        {
            return (new Status\Ok(["Deleted" => "Successfully deleted facility and its tags"]))->send();
        }
        else{
            return (new Status\NotFound(["Error" => "Unable to delete facility and its tags"]))->send();
        }
    }

    public function searchitems($searchq){
        $searchquery = "SELECT * FROM facility WHERE name Like '%".$searchq."%'
                        UNION
                        SELECT * FROM facility WHERE id IN ( SELECT fac_id from location WHERE city LIKE '%".$searchq."%')
                        UNION
                        SELECT * FROM facility WHERE id IN
                        (SELECT fac_id FROM factagjun WHERE tag_id in
                        (SELECT id FROM tag WHERE tagname LIKE '%".$searchq."%')
                        )";
        $tagquery = "SELECT tagname from tag WHERE id in (SELECT tag_id from factagjun WHERE fac_id = ?)";
        if($this->db->executeQuery($searchquery))
        {
            $res = $this->db->getStatement();
            if($res->rowCount() > 0)
            {
                $facilarr = [];
                while($row = $res->fetch(PDO::FETCH_ASSOC))
                {
                    extract($row);
                    $this->db->executeQuery($tagquery,[$id]);
                    $tgres = $this->db->getStatement();
                    $tg = $tgres->fetchAll(PDO::FETCH_ASSOC);
                    $tgarr = [];
                    foreach($tg as $vl)
                    {
                        array_push($tgarr,$vl['tagname']);
                    }
                    $er = array(
                        "id" => $id,
                        "Facility_Name" => $name,
                        "Date_of_Creation" => $doc,
                        "Tags" => $tgarr
                    );
                    array_push($facilarr,$er);
                }
                return (new Status\Ok($facilarr))->send();
            }
            else{
                return (new expt\NotFound(['Finished'=>'No results found']))->send();
            }

        }else{
            return (new Status\BadRequest(["Error" => "Cannot initiate Searchquery"]))->send();
        }
    }
}