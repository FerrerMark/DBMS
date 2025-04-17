<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function connect(Request $request)
    {
        $host = $request->input('host');
        $dbname = $request->input('dbname');
        $username = $request->input('dbusername');
        $password = $request->input('dbpass');

        try {
            config(['database.connections.dynamic' => [
                'driver' => 'mysql',
                'host' => $host,
                'database' => $dbname,
                'username' => $username,
                'password' => $password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]]);

            DB::connection('dynamic')->getPdo();

            // Store the current connection
            $currentConnection = [
                'host' => $host,
                'dbname' => $dbname,
                'username' => $username,
                'password' => $password,
            ];
            session(['dynamic_connection' => $currentConnection]);

            // Store in previous connections, ensuring uniqueness
            $previousConnections = session('previous_connections', []);
            $newConnection = [
                'host' => $host,
                'dbname' => $dbname,
                'username' => $username,
            ];
            $connectionExists = false;
            foreach ($previousConnections as $conn) {
                if ($conn['host'] === $host && $conn['dbname'] === $dbname && $conn['username'] === $username) {
                    $connectionExists = true;
                    break;
                }
            }
            if (!$connectionExists) {
                $previousConnections[] = $newConnection;
                session(['previous_connections' => $previousConnections]);
            }

            return redirect()->back()->with('output', 'Connected successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('output', 'Connection failed: ' . $e->getMessage());
        }
    }

    public function execute(Request $request)
    {
        $command = $request->input('cmd');

        if (empty($command)) {
            return redirect()->back()->with('output', 'Error: Command cannot be empty.');
        }

        $connection = session('dynamic_connection');
        if (!$connection) {
            return redirect()->back()->with('output', 'Error: No active database connection. Please connect first.');
        }

        config(['database.connections.dynamic' => [
            'driver' => 'mysql',
            'host' => $connection['host'],
            'database' => $connection['dbname'],
            'username' => $connection['username'],
            'password' => $connection['password'],
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]]);

        $dangerousKeywords = ['DROP', 'TRUNCATE', 'ALTER'];
        $command = trim($command);
        foreach ($dangerousKeywords as $keyword) {
            if (stripos($command, $keyword) !== false) {
                return redirect()->back()->with('output', 'Error: Potentially dangerous command detected.');
            }
        }

        try {
            $commandType = strtoupper(substr($command, 0, strpos($command, ' ') ?: strlen($command)));

            if (in_array($commandType, ['DELETE', 'UPDATE'])) {
                if (stripos($command, 'WHERE') === false) {
                    return redirect()->back()->with('output', 'Error: DELETE and UPDATE commands must include a WHERE clause.');
                }
            }

            switch ($commandType) {
                case 'SELECT':
                case 'SHOW':
                    $results = DB::connection('dynamic')->select($command);
                    if (empty($results)) {
                        return redirect()->back()->with('output', 'No results found.');
                    }
                    session(['last_command' => $command]);
                    return redirect()->back()->with('results', $results);

                case 'INSERT':
                    $affected = DB::connection('dynamic')->insert($command);
                    session(['last_command' => $command]);
                    return redirect()->back()->with('output', "Successfully inserted $affected row(s).");

                case 'UPDATE':
                    $affected = DB::connection('dynamic')->update($command);
                    session(['last_command' => $command]);
                    return redirect()->back()->with('output', "Successfully updated $affected row(s).");

                case 'DELETE':
                    $affected = DB::connection('dynamic')->delete($command);
                    session(['last_command' => $command]);
                    return redirect()->back()->with('output', "Successfully deleted $affected row(s).");

                default:
                    $results = DB::connection('dynamic')->select($command);
                    session(['last_command' => $command]);
                    if (empty($results)) {
                        return redirect()->back()->with('output', 'Command executed successfully, no results returned.');
                    }
                    return redirect()->back()->with('results', $results);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('output', 'Error: ' . $e->getMessage());
        }
    }
}
?>