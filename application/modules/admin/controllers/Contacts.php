<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Contacts extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        verify_session('admin');

        $this->load->model('contact_model', 'contact');
        $this->load->library('form_validation');

        // ini library buat ngirim email
        require APPPATH.'libraries/phpmailer/src/Exception.php';
        require APPPATH.'libraries/phpmailer/src/PHPMailer.php';
        require APPPATH.'libraries/phpmailer/src/SMTP.php';
    }

    public function index()
    {
        $params['title'] = 'Kelola Kontak Pengunjung';

        $this->load->view('header', $params);
        $this->load->view('contacts/contacts');
        $this->load->View('footer');
    }

    public function view($id = 0)
    {
        if ( $this->contact->is_contact_exist($id))
        {
            $data = $this->contact->contact_data($id);

            $params['title'] = 'Kontak '. $data->name;

            $contact['contact'] = $data;
            $contact['flash'] = $this->session->flashdata('contact_flash');

            $this->contact->set_status($id, 2);

            $this->load->view('header', $params);
            $this->load->view('contacts/view', $contact);
            $this->load->View('footer');
        }
        else
        {
            show_404();
        }
    }

    public function reply()
    {
        $id = $this->input->post('id');
        $sender = $this->input->post('email');
        $name = $this->input->post('name');
        $send_to = $this->input->post('to');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');

        $this->load->library('email');

        $this->email->from($sender, $name);
        $this->email->to($send_to);

        $this->email->subject($subject);
        $this->email->message($message);

        $this->email->send();
        $this->email->print_debugger(array('headers'));
    }



    // nyoba fungsi kirim email
    public function send()
 {
   // PHPMailer object
   $response = false;
   $mail = new PHPMailer();

   // SMTP configuration
   $mail->isSMTP();
   $mail->Host     = 'smtp.gmail.com';
   $mail->SMTPAuth = true;
   $mail->Username = 'dickyzs155@gmail.com'; // user email anda
   $mail->Password = 'gkgcvvvgzvcxgvkz'; // diisi dengan App Password yang sudah di generate
   $mail->SMTPSecure = 'ssl';
   $mail->Port     = 465;

   $mail->setFrom('dickyzs155@gmail.com', 'dicky'); // user email anda
   $mail->addReplyTo('dickyzs155@gmail.com', ''); //user email anda
   

   // Email subject
   $mail->Subject = 'SMTP CodeIgniter | dicky'; //subject email

   // Add a recipient
   $mail->addAddress($this->input->post('to')); //email tujuan pengiriman email

   // Set email format to HTML
   $mail->isHTML(true);


//    ini isian emailnya
// $mailContent = array (
// $id = $this->input->post('id'),
//         $sender = $this->input->post('email'),
//         $name = $this->input->post('name'),
//         $send_to = $this->input->post('to'),
//         $subject = $this->input->post('subject'),
//         $message = $this->input->post('message'),
// );
        


   // Email body content
   $mailContent = "<p>Hallo <b>".$this->input->post('nama')."</b> Halo terimakasih sudah menghubungi kami:</p>
   <table>
     <tr>
       <td>Nama</td>
       <td>:</td>
       <td>".$this->input->post('name')."</td>
     </tr>
     <tr>
       <td>Subject</td>
       <td>:</td>
       <td>".$this->input->post('subject')."</td>
     </tr>
     <tr>
       <td>Pesan</td>
       <td>:</td>
       <td>".$this->input->post('message')."</td>
     </tr>
   </table>
   <p>Terimakasih <b>".$this->input->post('nama')."</b> telah memberi komentar.</p>"; 
   // isi email

   $mail->Body = $mailContent;

   // Send email
   if(!$mail->send()){
     echo 'Message could not be sent.';
     echo 'Mailer Error: ' . $mail->ErrorInfo;
   }else{
     echo 'Message has been sent';
   }
 }



// batas bawah ngirim email




    public function api($action = '')
    {
        switch ($action)
        {
            case 'contacts' :
                $contacts['data'] = $this->contact->get_all_contacts();

                $response = $contacts;
            break;
            case 'delete' :
                $id = $this->input->post('id');

                $this->contact->delete_contact($id);

                $response = array('code' => 204);
            break;
        }

        $response = json_encode($response);
        $this->output->set_content_type('application/json')
            ->set_output($response);
    }
}