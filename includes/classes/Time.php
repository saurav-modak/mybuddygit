<?php

class Time {
    //private $user_obj;
    //private $con;

    public function __construct(){
        //$this->con = $con;
    }

    public function timeframe($date_time){

        //Timeframe
        $date_time_now = date("Y-m-d H:i:s");
        $start_date= new DateTime($date_time);//Time of post
        /*
            TODO: DateTime is a class comes with php
            ! Learn about it */
        $end_date= new DateTime($date_time_now); //Current time

        $interval = $start_date->diff($end_date);
        /*
            TODO: diff is a class comes with php
            ! Learn about it */
        //time msg
        if($interval->y>=1){
            if($interval==1)
                $time_message=$interval->y." Year ago";//1 year ago
            else
                $time_message=$interval->y." Years ago";//1+ year ago
        }

        else if($interval->m >= 1){
            if($interval->d==0){
                $days=" ago";
            }
            else if($interval -> d ==1){
                $days=$interval->d." Day ago";
            }
            else{
                $days=$interval->d." Days ago";
            }

            if($interval->m==1){
                $time_message=$interval->m." Moth".$days;
            }
            else{
                $time_message=$interval->m." Moths".$days; 
            }
        }

        else if($interval->d>=1){
            if($interval -> d ==1){
                $time_message="Yesterday";
            }
            else{
                $time_message=$interval->d." Days ago";
            }
        }

        else if($interval->h>=1){
            if($interval->h==1){
                $time_message=$interval->h." Hour ago";
            }
            else{
                $time_message=$interval->h." Hours ago";
            }
        }

        else if($interval->i>=1){
            if($interval->i==1){
                $time_message=$interval->i." Minute ago";
            }
            else{
                $time_message=$interval->i." Minutes ago";
            }
        }

        else{
            if($interval->s<30){
                $time_message=" Just now";
            }
            else{
                $time_message=$interval->s." Seconds ago";
            }
        }

        return $time_message;
    }
}

