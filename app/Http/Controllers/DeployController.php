<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;


class DeployController extends Controller
{
    public function DeployApps(Request $request)
    {
       $gitPayload = $request->getContent();
       $headerSignature = $request->header('X-Gitea-Signature');
       $secretKey = env('DEPLOY_KEY');
       $payloadSignature = hash_hmac('sha256', $gitPayload, $secretKey, false);
       
       if ($headerSignature != $payloadSignature) {
           error_log('FAILED - payload signature');
           exit();
       }
       
       $root_path = base_path();
       $process = Process::fromShellCommandline('cd .. && ./deploy.sh');
       $process->run(function ($type, $buffer) {
           echo $buffer;
       });
    }
}
