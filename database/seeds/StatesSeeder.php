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
        $stateData = [
            [
                'state_name' => 'Alabama',
                'state_acronym' => 'AL'
            ],
            [
                'state_acronym' =>'AK',
                'state_name' =>'Alaska'],
            [
                'state_acronym' =>'AZ',
                'state_name' =>'Arizona'],
            [
                'state_acronym' =>'AR','state_name' =>'Arkansas'],
            [
                'state_acronym' =>'CA','state_name' =>'California'],
            [
                'state_acronym' =>'CO','state_name' =>'Colorado'],
            [
                'state_acronym' =>'CT','state_name' =>'Connecticut'],
            [
                'state_acronym' =>'DE','state_name' =>'Delaware'],
            [
                'state_acronym' =>'DC','state_name' =>'District of Columbia'],
            [
                'state_acronym' =>'FL','state_name' =>'Florida'],
            [
                'state_acronym' =>'GA','state_name' =>'Georgia'],
            [
                'state_acronym' =>'HI','state_name' =>'Hawaii'],
            [
                'state_acronym' =>'ID','state_name' =>'Idaho'],
            [
                'state_acronym' =>'IL','state_name' =>'Illinois'],
            [
                'state_acronym' =>'IN','state_name' =>'Indiana'],
            [
                'state_acronym' =>'IA','state_name' =>'Iowa'],
            [
                'state_acronym' =>'KS','state_name' =>'Kansas'],
            [
                'state_acronym' =>'KY','state_name' =>'Kentucky'],
            [
                'state_acronym' =>'LA','state_name' =>'Louisiana'],
            [
                'state_acronym' =>'ME','state_name' =>'Maine'],
            [
                'state_acronym' =>'MD','state_name' =>'Maryland'],
            [
                'state_acronym' =>'MA','state_name' =>'Massachusetts'],
            [
                'state_acronym' =>'MI','state_name' =>'Michigan'],
            [
                'state_acronym' =>'MN','state_name' =>'Minnesota'],
            [
                'state_acronym' =>'MS','state_name' =>'Mississippi'],
            [
                'state_acronym' =>'MO','state_name' >'Missouri'],
            [
                'state_acronym' =>'MT','state_name' =>'Montana'],
            [
                'state_acronym' =>'NE','state_name' =>'Nebraska'],
            [
                'state_acronym' =>'NV','state_name' =>'Nevada'],
            [
                'state_acronym' =>'NH','state_name' =>'New Hampshire'],
            [
                'state_acronym' =>'NJ','state_name' =>'New Jersey'],
            [
                'state_acronym' =>'NM','state_name' =>'New Mexico'],
            [
                'state_acronym' =>'NY','state_name' =>'New York'],
            [
                'state_acronym' =>'NC','state_name' =>'North Carolina'],
            [
                'state_acronym' =>'ND','state_name' =>'North Dakota'],
            [
                'state_acronym' =>'OH','state_name' =>'Ohio'],
            [
                'state_acronym' =>'OK','state_name' =>'Oklahoma'],
            [
                'state_acronym' =>'OR','state_name' =>'Oregon'],
            [
                'state_acronym' =>'PA','state_name' =>'Pennsylvania'],
            [
                'state_acronym' =>'RI','state_name' =>'Rhode Island'],
            [
                'state_acronym' =>'SC','state_name' =>'South Carolina'],
            [
                'state_acronym' =>'SD','state_name' =>'South Dakota'],
            [
                'state_acronym' =>'TN','state_name' =>'Tennessee'],
            [
                'state_acronym' =>'TX','state_name' =>'Texas'],
            [
                'state_acronym' =>'UT','state_name' =>'Utah'],
            [
                'state_acronym' =>'VT','state_name' =>'Vermont'],
            [
                'state_acronym' =>'VA','state_name' =>'Virginia'],
            [
                'state_acronym' =>'WA','state_name' =>'Washington'],
            [
                'state_acronym' =>'WV','state_name' =>'West Virginia'],
            [
                'state_acronym' =>'WI','state_name' =>'Wisconsin'],
            [
                'state_acronym' =>'WY','state_name' =>'Wyoming'
            ],
        ];
        DB::table('states')->insert($stateData);
    }
}
