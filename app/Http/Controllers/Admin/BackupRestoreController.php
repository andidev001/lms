<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupRestoreController extends Controller
{
    /**
     * Menampilkan halaman pengelola Backup dan Restore Database
     */
    public function index()
    {
        $dbName = DB::connection()->getDatabaseName();
        $tables = DB::select('SHOW TABLES');
        $totalTables = count($tables);
        $driver = config('database.default', 'mysql');

        return view('admin.backup.index', compact('dbName', 'totalTables', 'driver'));
    }

    /**
     * Memproses download file backup database (.sql)
     */
    public function backup()
    {
        try {
            $databaseName = DB::connection()->getDatabaseName();
            $tables = DB::select('SHOW TABLES');

            $output = "-- =========================================================\n";
            $output .= "-- BACKUP DATABASE SISTEM MANAJEMEN PEMBELAJARAN (LMS)\n";
            $output .= "-- Database: {$databaseName}\n";
            $output .= "-- Tanggal Backup: " . date('Y-m-d H:i:s') . "\n";
            $output .= "-- =========================================================\n\n";
            $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $tableArray = array_values((array) $table);
                $tableName = $tableArray[0];

                // Ambil struktur tabel (CREATE TABLE)
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createTableArray = array_values((array) $createTable[0]);
                $createStatement = $createTableArray[1];

                $output .= "-- --------------------------------------------------------\n";
                $output .= "-- Struktur untuk tabel `{$tableName}`\n";
                $output .= "-- --------------------------------------------------------\n";
                $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $output .= $createStatement . ";\n\n";

                // Ambil data dalam tabel (INSERT INTO)
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $output .= "-- Dumping data untuk tabel `{$tableName}`\n";
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $values = [];
                        foreach ($rowArray as $value) {
                            if (is_null($value)) {
                                $values[] = "NULL";
                            } else {
                                // Quoting aman dari PDO untuk menangani string & karakter spesial
                                $values[] = DB::getPdo()->quote($value);
                            }
                        }
                        $valuesString = implode(', ', $values);
                        $output .= "INSERT INTO `{$tableName}` VALUES ({$valuesString});\n";
                    }
                    $output .= "\n";
                }
            }

            $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

            $fileName = 'Backup_LMS_' . date('Y_m_d_H_i_s') . '.sql';

            return response()->streamDownload(function () use ($output) {
                echo $output;
            }, $fileName, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\""
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal melakukan backup database: ' . $e->getMessage());
        }
    }

    /**
     * Memproses pemulihan data dari file .sql yang diunggah
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ], [
            'backup_file.required' => 'Silakan pilih file backup (.sql) terlebih dahulu.'
        ]);

        $file = $request->file('backup_file');
        $ext = strtolower($file->getClientOriginalExtension());

        if ($ext !== 'sql' && $file->getClientMimeType() !== 'application/sql' && $file->getClientMimeType() !== 'text/plain') {
            return back()->with('error', 'File yang diupload harus berformat .sql yang valid.');
        }

        try {
            $sql = file_get_contents($file->getRealPath());

            if (empty(trim($sql))) {
                return back()->with('error', 'File backup yang diupload kosong.');
            }

            // Eksekusi pemulihan database
            DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared($sql);
            DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');

            return back()->with('success', 'Database berhasil dipulihkan (Restore) dari file backup!');

        } catch (\Exception $e) {
            DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('error', 'Gagal melakukan restore database! Error: ' . $e->getMessage());
        }
    }
}
