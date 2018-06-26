<?php

use Illuminate\Database\Seeder;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // PUT IN STATE DATA
        $stateData = array(
            array(
                'state_name' => 'Alabama',
                'state_acronym' => 'AL'
            ),
            array(
                'state_acronym' =>'AK',
                'state_name' =>'Alaska'),
            array(
                'state_acronym' =>'AZ',
                'state_name' =>'Arizona'),
            array(
                'state_acronym' =>'AR','state_name' =>'Arkansas'),
            array(
                'state_acronym' =>'CA','state_name' =>'California'),
            array(
                'state_acronym' =>'CO','state_name' =>'Colorado'),
            array(
                'state_acronym' =>'CT','state_name' =>'Connecticut'),
            array(
                'state_acronym' =>'DE','state_name' =>'Delaware'),
            array(
                'state_acronym' =>'DC','state_name' =>'District of Columbia'),
            array(
                'state_acronym' =>'FL','state_name' =>'Florida'),
            array(
                'state_acronym' =>'GA','state_name' =>'Georgia'),
            array(
                'state_acronym' =>'HI','state_name' =>'Hawaii'),
            array(
                'state_acronym' =>'ID','state_name' =>'Idaho'),
            array(
                'state_acronym' =>'IL','state_name' =>'Illinois'),
            array(
                'state_acronym' =>'IN','state_name' =>'Indiana'),
            array(
                'state_acronym' =>'IA','state_name' =>'Iowa'),
            array(
                'state_acronym' =>'KS','state_name' =>'Kansas'),
            array(
                'state_acronym' =>'KY','state_name' =>'Kentucky'),
            array(
                'state_acronym' =>'LA','state_name' =>'Louisiana'),
            array(
                'state_acronym' =>'ME','state_name' =>'Maine'),
            array(
                'state_acronym' =>'MD','state_name' =>'Maryland'),
            array(
                'state_acronym' =>'MA','state_name' =>'Massachusetts'),
            array(
                'state_acronym' =>'MI','state_name' =>'Michigan'),
            array(
                'state_acronym' =>'MN','state_name' =>'Minnesota'),
            array(
                'state_acronym' =>'MS','state_name' =>'Mississippi'),
            array(
                'state_acronym' =>'MO','state_name' >'Missouri'),
            array(
                'state_acronym' =>'MT','state_name' =>'Montana'),
            array(
                'state_acronym' =>'NE','state_name' =>'Nebraska'),
            array(
                'state_acronym' =>'NV','state_name' =>'Nevada'),
            array(
                'state_acronym' =>'NH','state_name' =>'New Hampshire'),
            array(
                'state_acronym' =>'NJ','state_name' =>'New Jersey'),
            array(
                'state_acronym' =>'NM','state_name' =>'New Mexico'),
            array(
                'state_acronym' =>'NY','state_name' =>'New York'),
            array(
                'state_acronym' =>'NC','state_name' =>'North Carolina'),
            array(
                'state_acronym' =>'ND','state_name' =>'North Dakota'),
            array(
                'state_acronym' =>'OH','state_name' =>'Ohio'),
            array(
                'state_acronym' =>'OK','state_name' =>'Oklahoma'),
            array(
                'state_acronym' =>'OR','state_name' =>'Oregon'),
            array(
                'state_acronym' =>'PA','state_name' =>'Pennsylvania'),
            array(
                'state_acronym' =>'RI','state_name' =>'Rhode Island'),
            array(
                'state_acronym' =>'SC','state_name' =>'South Carolina'),
            array(
                'state_acronym' =>'SD','state_name' =>'South Dakota'),
            array(
                'state_acronym' =>'TN','state_name' =>'Tennessee'),
            array(
                'state_acronym' =>'TX','state_name' =>'Texas'),
            array(
                'state_acronym' =>'UT','state_name' =>'Utah'),
            array(
                'state_acronym' =>'VT','state_name' =>'Vermont'),
            array(
                'state_acronym' =>'VA','state_name' =>'Virginia'),
            array(
                'state_acronym' =>'WA','state_name' =>'Washington'),
            array(
                'state_acronym' =>'WV','state_name' =>'West Virginia'),
            array(
                'state_acronym' =>'WI','state_name' =>'Wisconsin'),
            array(
                'state_acronym' =>'WY','state_name' =>'Wyoming'
            ),
        );
        DB::table('states')->insert($stateData);
    }
}
