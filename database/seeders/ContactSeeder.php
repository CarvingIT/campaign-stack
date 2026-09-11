<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            // Tech & Startups (Bangalore, Pune, Hyderabad, Gurgaon)
            ['salutation' => 'Mr.', 'firstname' => 'Vikram', 'lastname' => 'Aditya', 'email' => 'vikram.aditya@razorpay.com', 'company' => 'Razorpay Software', 'mobile' => '+91 98201 44521'],
            ['salutation' => 'Ms.', 'firstname' => 'Ananya', 'lastname' => 'Iyer', 'email' => 'ananya.iyer@freshworks.com', 'company' => 'Freshworks Technologies', 'mobile' => '+91 98450 12389'],
            ['salutation' => 'Mr.', 'firstname' => 'Rohan', 'lastname' => 'Deshmukh', 'email' => 'rohan.deshmukh@zerodha.com', 'company' => 'Zerodha Broking', 'mobile' => '+91 99234 56781'],
            ['salutation' => 'Ms.', 'firstname' => 'Pooja', 'lastname' => 'Kulkarni', 'email' => 'pooja.k@postman.com', 'company' => 'Postman India', 'mobile' => '+91 98812 34567'],
            ['salutation' => 'Mr.', 'firstname' => 'Aditya', 'lastname' => 'Menon', 'email' => 'aditya.menon@swiggy.in', 'company' => 'Swiggy Bundl Tech', 'mobile' => '+91 97401 98765'],
            ['salutation' => 'Ms.', 'firstname' => 'Sneha', 'lastname' => 'Reddy', 'email' => 'sneha.reddy@zomato.com', 'company' => 'Zomato Media Pvt Ltd', 'mobile' => '+91 99890 11223'],
            ['salutation' => 'Mr.', 'firstname' => 'Karthik', 'lastname' => 'Ramanathan', 'email' => 'karthik.r@cred.club', 'company' => 'Dreamplug Technologies (CRED)', 'mobile' => '+91 98402 33445'],
            ['salutation' => 'Ms.', 'firstname' => 'Divya', 'lastname' => 'Chawla', 'email' => 'divya.chawla@flipkart.com', 'company' => 'Flipkart Internet Pvt Ltd', 'mobile' => '+91 98110 55667'],
            ['salutation' => 'Mr.', 'firstname' => 'Siddharth', 'lastname' => 'Verma', 'email' => 'siddharth.v@inmobi.com', 'company' => 'InMobi Technologies', 'mobile' => '+91 98451 66778'],
            ['salutation' => 'Ms.', 'firstname' => 'Meera', 'lastname' => 'Nambiar', 'email' => 'meera.nambiar@zoho.com', 'company' => 'Zoho Corporation', 'mobile' => '+91 94440 77889'],

            // IT & Enterprise Leaders (Mumbai, Delhi-NCR, Chennai, Kolkata)
            ['salutation' => 'Mr.', 'firstname' => 'Rajesh', 'lastname' => 'Patel', 'email' => 'rajesh.patel@tcs.com', 'company' => 'Tata Consultancy Services', 'mobile' => '+91 98200 88990'],
            ['salutation' => 'Ms.', 'firstname' => 'Sunita', 'lastname' => 'Singhania', 'email' => 'sunita.s@infosys.com', 'company' => 'Infosys Limited', 'mobile' => '+91 98452 99001'],
            ['salutation' => 'Mr.', 'firstname' => 'Amit', 'lastname' => 'Bhattacharya', 'email' => 'amit.bhatt@wipro.com', 'company' => 'Wipro Enterprises', 'mobile' => '+91 98300 11224'],
            ['salutation' => 'Ms.', 'firstname' => 'Kavita', 'lastname' => 'Joshi', 'email' => 'kavita.joshi@tataelxsi.co.in', 'company' => 'Tata Elxsi Ltd', 'mobile' => '+91 98210 22335'],
            ['salutation' => 'Mr.', 'firstname' => 'Sanjay', 'lastname' => 'Gupta', 'email' => 'sanjay.gupta@hcl.com', 'company' => 'HCLTech Enterprise', 'mobile' => '+91 98100 33446'],
            ['salutation' => 'Ms.', 'firstname' => 'Neelam', 'lastname' => 'Kapoor', 'email' => 'neelam.k@ltimindtree.com', 'company' => 'LTIMindtree Global', 'mobile' => '+91 98202 44557'],
            ['salutation' => 'Mr.', 'firstname' => 'Gaurav', 'lastname' => 'Saxena', 'email' => 'gaurav.saxena@techmahindra.com', 'company' => 'Tech Mahindra Ltd', 'mobile' => '+91 98180 55668'],
            ['salutation' => 'Ms.', 'firstname' => 'Swati', 'lastname' => 'Natarajan', 'email' => 'swati.n@persistent.com', 'company' => 'Persistent Systems', 'mobile' => '+91 98810 66779'],
            ['salutation' => 'Mr.', 'firstname' => 'Abhishek', 'lastname' => 'Banerjee', 'email' => 'abhishek.b@cyient.com', 'company' => 'Cyient Solutions', 'mobile' => '+91 98310 77880'],
            ['salutation' => 'Ms.', 'firstname' => 'Ritu', 'lastname' => 'Agarwal', 'email' => 'ritu.agarwal@mphasis.com', 'company' => 'Mphasis India', 'mobile' => '+91 98203 88991'],

            // Higher Education & Academic Directors (IITs, IIMs, Universities, Koha Users)
            ['salutation' => 'Dr.', 'firstname' => 'Manoj', 'lastname' => 'Tripathi', 'email' => 'librarian@iitb.ac.in', 'company' => 'IIT Bombay Central Library', 'mobile' => '+91 98204 12121'],
            ['salutation' => 'Prof.', 'firstname' => 'Sujata', 'lastname' => 'Venkataraman', 'email' => 'library.head@iitm.ac.in', 'company' => 'IIT Madras Information Centre', 'mobile' => '+91 94441 23232'],
            ['salutation' => 'Dr.', 'firstname' => 'Pradeep', 'lastname' => 'Rao', 'email' => 'director.lib@iitd.ac.in', 'company' => 'IIT Delhi Knowledge Hub', 'mobile' => '+91 98111 34343'],
            ['salutation' => 'Prof.', 'firstname' => 'Aravind', 'lastname' => 'Swaminathan', 'email' => 'aravind.s@iima.ac.in', 'company' => 'IIM Ahmedabad Library', 'mobile' => '+91 98250 45454'],
            ['salutation' => 'Dr.', 'firstname' => 'Alok', 'lastname' => 'Mishra', 'email' => 'dean.digital@iitk.ac.in', 'company' => 'IIT Kanpur IT Systems', 'mobile' => '+91 94150 56565'],
            ['salutation' => 'Prof.', 'firstname' => 'Geeta', 'lastname' => 'Mukherjee', 'email' => 'geeta.m@jnu.ac.in', 'company' => 'Jawaharlal Nehru University', 'mobile' => '+91 98101 67676'],
            ['salutation' => 'Dr.', 'firstname' => 'Venkatesh', 'lastname' => 'Prasad', 'email' => 'library@iisc.ac.in', 'company' => 'IISc Bangalore Digital Library', 'mobile' => '+91 98453 78787'],
            ['salutation' => 'Prof.', 'firstname' => 'Nalini', 'lastname' => 'Sundaram', 'email' => 'nalini.s@bits-pilani.ac.in', 'company' => 'BITS Pilani Academic Network', 'mobile' => '+91 98290 89898'],
            ['salutation' => 'Dr.', 'firstname' => 'Harish', 'lastname' => 'Choudhury', 'email' => 'h.choudhury@du.ac.in', 'company' => 'Delhi University Central System', 'mobile' => '+91 98112 90909'],
            ['salutation' => 'Dr.', 'firstname' => 'Shalini', 'lastname' => 'Bhatia', 'email' => 'shalini.b@aiims.edu', 'company' => 'AIIMS New Delhi Research Hub', 'mobile' => '+91 98102 01010'],

            // Fintech, BFSI & Manufacturing (HDFC, ICICI, Reliance, L&T, Bajaj)
            ['salutation' => 'Mr.', 'firstname' => 'Kunal', 'lastname' => 'Shah', 'email' => 'kunal.shah@hdfcbank.com', 'company' => 'HDFC Bank Technology Wing', 'mobile' => '+91 98205 11122'],
            ['salutation' => 'Ms.', 'firstname' => 'Aparna', 'lastname' => 'Sen', 'email' => 'aparna.sen@icicibank.com', 'company' => 'ICICI Bank Digital Systems', 'mobile' => '+91 98206 22233'],
            ['salutation' => 'Mr.', 'firstname' => 'Nitin', 'lastname' => 'Gadgil', 'email' => 'nitin.gadgil@bajajfinserv.in', 'company' => 'Bajaj Finserv Ltd', 'mobile' => '+91 98811 33344'],
            ['salutation' => 'Ms.', 'firstname' => 'Tanvi', 'lastname' => 'Pandey', 'email' => 'tanvi.p@kotak.com', 'company' => 'Kotak Mahindra Capital', 'mobile' => '+91 98207 44455'],
            ['salutation' => 'Mr.', 'firstname' => 'Vivek', 'lastname' => 'Namboodiri', 'email' => 'vivek.n@ril.com', 'company' => 'Reliance Industries Jio Platforms', 'mobile' => '+91 98208 55566'],
            ['salutation' => 'Ms.', 'firstname' => 'Deepika', 'lastname' => 'Bhardwaj', 'email' => 'deepika.b@larsentoubro.com', 'company' => 'Larsen & Toubro Infotech', 'mobile' => '+91 98209 66677'],
            ['salutation' => 'Mr.', 'firstname' => 'Manish', 'lastname' => 'Tiwari', 'email' => 'manish.tiwari@tatamotors.com', 'company' => 'Tata Motors Digital Engineering', 'mobile' => '+91 98812 77788'],
            ['salutation' => 'Ms.', 'firstname' => 'Rashmi', 'lastname' => 'Hegde', 'email' => 'rashmi.hegde@titan.co.in', 'company' => 'Titan Company Ltd', 'mobile' => '+91 98454 88899'],
            ['salutation' => 'Mr.', 'firstname' => 'Pranav', 'lastname' => 'Somani', 'email' => 'pranav.somani@maruti.co.in', 'company' => 'Maruti Suzuki India', 'mobile' => '+91 98103 99900'],
            ['salutation' => 'Ms.', 'firstname' => 'Radhika', 'lastname' => 'Mehta', 'email' => 'radhika.mehta@asianpaints.com', 'company' => 'Asian Paints IT Systems', 'mobile' => '+91 98211 12345'],

            // Additional Founders & Fast-growing Indian SMEs
            ['salutation' => 'Mr.', 'firstname' => 'Saurabh', 'lastname' => 'Jain', 'email' => 'saurabh@groww.in', 'company' => 'Nextbillion Technology (Groww)', 'mobile' => '+91 98455 23456'],
            ['salutation' => 'Ms.', 'firstname' => 'Ankita', 'lastname' => 'Sharma', 'email' => 'ankita.s@clevertap.com', 'company' => 'CleverTap India', 'mobile' => '+91 98212 34567'],
            ['salutation' => 'Mr.', 'firstname' => 'Varun', 'lastname' => 'Khanna', 'email' => 'varun.k@urbancompany.com', 'company' => 'Urban Company (UrbanClap)', 'mobile' => '+91 98104 45678'],
            ['salutation' => 'Ms.', 'firstname' => 'Ishita', 'lastname' => 'Bose', 'email' => 'ishita.bose@zepto.in', 'company' => 'Zepto Kirana Commerce', 'mobile' => '+91 98213 56789'],
            ['salutation' => 'Mr.', 'firstname' => 'Tushar', 'lastname' => 'Garg', 'email' => 'tushar.garg@lenskart.com', 'company' => 'Lenskart Solutions', 'mobile' => '+91 98105 67890'],
            ['salutation' => 'Ms.', 'firstname' => 'Shreya', 'lastname' => 'Pillai', 'email' => 'shreya.pillai@nykaa.com', 'company' => 'FSN E-Commerce (Nykaa)', 'mobile' => '+91 98214 78901'],
            ['salutation' => 'Mr.', 'firstname' => 'Naveen', 'lastname' => 'Shetty', 'email' => 'naveen.shetty@phonepe.com', 'company' => 'PhonePe India', 'mobile' => '+91 98456 89012'],
            ['salutation' => 'Ms.', 'firstname' => 'Pallavi', 'lastname' => 'Rathi', 'email' => 'pallavi.rathi@delhivery.com', 'company' => 'Delhivery Logistics Solutions', 'mobile' => '+91 98106 90123'],
            ['salutation' => 'Mr.', 'firstname' => 'Chetan', 'lastname' => 'Bhardwaj', 'email' => 'chetan.b@shadowfax.in', 'company' => 'Shadowfax Technologies', 'mobile' => '+91 98457 01234'],
            ['salutation' => 'Ms.', 'firstname' => 'Bhavna', 'lastname' => 'Malhotra', 'email' => 'bhavna.m@spinny.com', 'company' => 'Valuedrive Technologies (Spinny)', 'mobile' => '+91 98107 12345'],
        ];

        foreach ($contacts as $contactData) {
            Contact::firstOrCreate(
                ['email' => $contactData['email']],
                $contactData
            );
        }
    }
}
