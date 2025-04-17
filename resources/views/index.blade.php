<x-layout>
    <div class="container" style="display: flex; height: 83vh; gap: 10px; background-color: #f4f4f9; padding: 10px;">
        <div class="fi" style="display: flex; flex-direction: column; gap: 10px; min-width: 300px;">
            <form method="POST" action="{{ route('connect') }}" style="border: 1px solid #3a3a4e; padding: 12px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label for="host" style="font-family: Arial, sans-serif; font-size: 14px; color: #1e1e2f;">Host:</label><br>
                    <input type="text" id="host" name="host" value="{{ session('dynamic_connection.host') ?: old('host', 'localhost') }}"
                        placeholder="e.g., localhost"
                        style="width: 100%; padding: 10px; border: 1px solid #3a3a4e; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 14px; background-color: #f9f9fc; color: #1e1e2f;" />
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="dbname" style="font-family: Arial, sans-serif; font-size: 14px; color: #1e1e2f;">Database Name:</label><br>
                    <input type="text" id="dbname" name="dbname" value="{{ session('dynamic_connection.dbname') ?: old('dbname', 'my_database') }}"
                        placeholder="e.g., my_database"
                        style="width: 100%; padding: 10px; border: 1px solid #3a3a4e; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 14px; background-color: #f9f9fc; color: #1e1e2f;" />
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="dbusername" style="font-family: Arial, sans-serif; font-size: 14px; color: #1e1e2f;">Username:</label><br>
                    <input type="text" id="dbusername" name="dbusername" value="{{ session('dynamic_connection.username') ?: old('dbusername', 'root') }}"
                        placeholder="e.g., root"
                        style="width: 100%; padding: 10px; border: 1px solid #3a3a4e; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 14px; background-color: #f9f9fc; color: #1e1e2f;" />
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="dbpass" style="font-family: Arial, sans-serif; font-size: 14px; color: #1e1e2f;">Password:</label><br>
                    <input type="password" id="dbpass" name="dbpass" value="{{ session('dynamic_connection.password') ?: old('dbpass', '') }}"
                        style="width: 100%; padding: 10px; border: 1px solid #3a3a4e; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 14px; background-color: #f9f9fc; color: #1e1e2f;" />
                </div>

                <button type="submit"
                    style="width: 100%; padding: 12px; background-color: #1e1e2f; color: #ffffff; border: none; border-radius: 4px; cursor: pointer; font-family: Arial, sans-serif; font-size: 14px; transition: background-color 0.2s;">
                    Connect
                </button>
            </form>
        
            <div class="cmd">
                <form method="POST" action="{{ route('execute') }}" style="border: 1px solid #3a3a4e; padding: 12px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    @csrf
                    <div style="margin-bottom: 16px;">
                        <label for="cmd" style="font-family: Arial, sans-serif; font-size: 14px; color: #1e1e2f;">Command:</label><br>
                        <textarea id="cmd" name="cmd" rows="1" placeholder="e.g., SELECT * FROM users" style="width: 100%; padding: 10px; border: 1px solid #3a3a4e; border-radius: 4px; resize: both; overflow: auto; min-width: 250px; max-width: 600px; min-height: 36px; font-family: 'Courier New', monospace; font-size: 14px; background-color: #f9f9fc; color: #1e1e2f;">{{ old('cmd') }}</textarea>
                    </div>
                    <button type="submit" style="width: 100%; padding: 12px; background-color: #1e1e2f; color: #ffffff; border: none; border-radius: 4px; cursor: pointer; font-family: Arial, sans-serif; font-size: 14px; transition: background-color 0.2s;">
                        Execute
                    </button>
                </form>
            </div>

            <div class="databases" style="border: 1px solid #3a3a4e; padding: 12px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="font-family: Arial, sans-serif; font-size: 16px; color: #1e1e2f; margin: 0 0 10px 0;">Previous Connections</h3>
                @if (session('previous_connections') && count(session('previous_connections')) > 0)
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach (session('previous_connections') as $conn)
                            <li style="padding: 8px; border-bottom: 1px solid #3a3a4e; font-family: 'Courier New', monospace; font-size: 14px; color: #1e1e2f; cursor: pointer; transition: background-color 0.2s;" 
                                onclick="document.getElementById('host').value='{{ $conn['host'] }}'; document.getElementById('dbname').value='{{ $conn['dbname'] }}'; document.getElementById('dbusername').value='{{ $conn['username'] }}'; document.getElementById('dbpass').value='';">
                                {{ $conn['host'] }} / {{ $conn['dbname'] }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="font-family: Arial, sans-serif; font-size: 14px; color: #1e1e2f; margin: 0;">No previous connections.</p>
                @endif
            </div>
        </div>

        <div class="output" style="border: 1px solid #3a3a4e; padding: 12px; background-color: #ffffff; border-radius: 8px; flex-grow: 1; overflow-x: auto; overflow-y: auto; max-height: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            @if (session('output'))
                <pre style="font-family: 'Courier New', monospace; font-size: 14px; color: #1e1e2f; margin: 0; white-space: pre-wrap;">{{ session('output') }}</pre>
            @elseif (session('results'))
                @if (is_array(session('results')) && count(session('results')) > 0)
                    <table style="border-collapse: collapse; width: 100%; font-family: 'Courier New', monospace; font-size: 14px;">
                        <thead>
                            <tr>
                                @foreach (array_keys(get_object_vars(session('results')[0])) as $column)
                                    <th style="border: 1px solid #3a3a4e; padding: 10px; background-color: #1e1e2f; color: #ffffff; text-align: left;">{{ $column }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (session('results') as $index => $row)
                                <tr style="background-color: {{ $index % 2 == 0 ? '#f9f9fc' : '#ffffff' }};">
                                    @foreach (get_object_vars($row) as $value)
                                        <td style="border: 1px solid #3a3a4e; padding: 10px; color: #1e1e2f;">{{ $value }}</td>
                                    @endforeach
                                </tr>
                            @endforeach 
                        </tbody>
                    </table>
                @else
                    <p style="font-family: Arial, sans-serif; font-size: 14px; color: #1e1e2f;">No results found.</p>
                @endif
            @endif
        </div>
    </div>
</x-layout>