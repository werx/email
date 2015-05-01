<?php
namespace werx\EmailTests;

use werx\Email;

/**
 * Class MailTests
 *
 * Based on http://codeception.com/12-15-2013/testing-emails-in-php.html
 *
 * @package joshmoody\Library\Tests
 */
class MailTests extends \PHPUnit_Framework_TestCase
{
	public $mailcatcher = null;
	public $config;

	public function __construct()
	{
		$config = [];
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = '127.0.0.1';
		$config['smtp_user'] = '';
		$config['smtp_pass'] = '';
		$config['smtp_port'] = '1025';
		$config['mailtype'] = 'html';

		$this->config = $config;

		$this->mailcatcher = new \GuzzleHttp\Client(['base_url' => 'http://127.0.0.1:1080']);
	}

	public function testCanSendMail()
	{
		$email = new Email\Message($this->config);

		$attachment = dirname(dirname(__FILE__)) . '/README.md';

		for ($i = 0; $i < 10; $i++) {
			$count = $i + 1;

			$this->cleanMessages();

			$subject = sprintf('Email Test # %s', $count);

			$body = '<p>Email Body</p>';

			$email->clear(true);
			$email->to('you@example.com');
			$email->from('me@example.com', 'Me');
			$email->subject($subject);
			$email->message($body);
			$email->attach($attachment);
			$success = $email->send();

			$this->assertTrue($success);

			$message = $this->getLastMessage();

			$this->assertEmailIsSent();
			$this->assertEmailRecipientsContain('<you@example.com>', $message);
			$this->assertEmailSubjectEquals($subject, $message);
			$this->assertEmailHtmlContains($body, $message);
			$this->assertEmailTextContains(strip_tags($body), $message, 'Plain text should be the body without HTML Tags');
			$this->assertEmailSenderEquals('<me@example.com>', $message);
			$this->assertEmailHasAttachment('README.md', $message, 'Message should have attachment named README.md');
		}
	}

	public function testCanParseSender()
	{
		$email = new Email\Message($this->config);

		$email->from('Foo Bar <foo@bar.com>');
		$this->assertEquals('"Foo Bar" <foo@bar.com>', $email->getHeader('from'));

		$email->from('Foo Bar<foo@bar.com>');
		$this->assertEquals('"Foo Bar" <foo@bar.com>', $email->getHeader('from'));

		$email->from('foo@bar.com', 'Foo Bar');
		$this->assertEquals('"Foo Bar" <foo@bar.com>', $email->getHeader('from'));

		$email->from('foo@bar.com', '');
		$this->assertEquals(' <foo@bar.com>', $email->getHeader('from'));

		$email->from('foo@bar.com', ' ');
		$this->assertEquals(' <foo@bar.com>', $email->getHeader('from'));
	}

	public function setUp()
	{
		// clean emails between tests
		$this->cleanMessages();
	}

	public function cleanMessages()
	{
		$this->mailcatcher->delete('/messages');
	}

	public function getLastMessage()
	{
		$messages = $this->getMessages();
		if (empty($messages)) {
			$this->fail("No messages received");
		}
		// messages are in descending order
		return (object)reset($messages);
	}

	public function getMessages()
	{
		return $this->mailcatcher->get('/messages')->json();
	}

	public function assertEmailIsSent($description = '')
	{
		$this->assertNotEmpty($this->getMessages(), $description);
	}

	public function assertEmailSubjectContains($needle, $email, $description = '')
	{
		$this->assertContains($needle, $email->subject, $description);
	}

	public function assertEmailSubjectEquals($expected, $email, $description = '')
	{
		$this->assertContains($expected, $email->subject, $description);
	}

	public function assertEmailHtmlContains($needle, $email, $description = '')
	{
		$response = $this->mailcatcher->get("/messages/{$email->id}.html");
		$this->assertContains($needle, (string)$response->getBody(), $description);
	}

	public function assertEmailTextContains($needle, $email, $description = '')
	{
		$response = $this->mailcatcher->get("/messages/{$email->id}.plain");
		$this->assertContains($needle, (string)$response->getBody(), $description);
	}

	public function assertEmailSenderEquals($expected, $email, $description = '')
	{
		$message = (object)$this->mailcatcher->get("/messages/{$email->id}.json")->json();
		$this->assertEquals($expected, $message->sender, $description);
	}

	public function assertEmailRecipientsContain($needle, $email, $description = '')
	{
		$message = (object)$this->mailcatcher->get("/messages/{$email->id}.json")->json();
		$this->assertContains($needle, $message->recipients, $description);
	}

	public function assertEmailHasAttachment($name, $email, $description = '')
	{
		$message = (object)$this->mailcatcher->get("/messages/{$email->id}.json")->json();

		$this->assertTrue(is_array($message->attachments) && count($message->attachments) > 0, 'Attachments should be an array');

		$has_attachment = false;

		// Look at all the attachments to see if there is one with the expected name.
		foreach ($message->attachments as $a) {
			if ($a['filename'] == $name) {
				$has_attachment = true;
			}
		}

		$this->assertTrue($has_attachment, 'There should be an attachment should be named ' . $name);
	}
}
