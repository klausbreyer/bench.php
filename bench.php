<?php
/**
 * PHP Benchmark Script for Command Line and Web with Enhanced Output
 *
 * This script benchmarks the CPU, memory, and IO performance of the server.
 * It provides clear and consistent results suitable for both command-line and web environments.
 */

/**
 * Get the current time in microseconds.
 *
 * @return float Current time in seconds with microseconds.
 */
function getTime()
{
	return microtime(true);
}

/**
 * Immediately output and flush the buffer.
 */
function flushOutput()
{
	if (ob_get_level() > 0) {
		ob_end_flush();
	}
	flush();
}

// Detect if the script is run via CLI or web
$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
	// If run via web, set header to plain text
	header('Content-Type: text/plain');
}

// Output header with date and PHP configuration
echo "PHP Benchmark Results\n";
echo "=====================\n\n";

echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Execution Time Limit: " . ini_get('max_execution_time') . " seconds\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Operating System: " . php_uname('s') . " " . php_uname('r') . "\n\n";

flushOutput();

/**
 * CPU Benchmark: Calculate the Fibonacci sequence recursively.
 *
 * @param int $iterations The Fibonacci number to calculate.
 * @return array The result and time taken in milliseconds.
 */
function cpuBenchmark($iterations = 30)
{
	/**
	 * Recursive function to calculate Fibonacci numbers.
	 *
	 * @param int $n The position in the Fibonacci sequence.
	 * @return int The Fibonacci number at position $n.
	 */
	function fibonacci($n)
	{
		if ($n <= 1)
			return $n;
		return fibonacci($n - 1) + fibonacci($n - 2);
	}

	$start = getTime();
	$result = fibonacci($iterations);
	$end = getTime();

	$duration_ms = ($end - $start) * 1000;

	return [
		'result' => $result,
		'time_ms' => $duration_ms
	];
}

/**
 * Memory Benchmark: Create and manipulate a large array.
 *
 * @return array The peak memory used in MB and the time taken in milliseconds.
 */
function memoryBenchmark()
{
	// Force garbage collection to clear memory
	gc_collect_cycles();

	$startMemory = memory_get_peak_usage(true);
	$startTime = getTime();

	// Create a large array
	$array = [];
	$totalIterations = 100000;

	for ($i = 0; $i < $totalIterations; $i++) {
		$array[] = md5($i);
	}

	// Manipulate the array
	sort($array);

	$endTime = getTime();
	$endMemory = memory_get_peak_usage(true);

	$memory_used_mb = ($endMemory - $startMemory) / (1024 * 1024);
	$duration_ms = ($endTime - $startTime) * 1000;

	return [
		'memory_used_mb' => $memory_used_mb,
		'time_ms' => $duration_ms
	];
}

/**
 * IO Benchmark: Write and read a large file.
 *
 * @param string $filename The name of the temporary file.
 * @return array Bytes written, write time, and read time in milliseconds.
 */
function ioBenchmark($filename = 'benchmark_test_file.tmp')
{
	$data = str_repeat("The quick brown fox jumps over the lazy dog.\n", 10000); // approx. 450KB

	// Write to file
	$startWrite = getTime();
	$bytesWritten = file_put_contents($filename, $data);
	$endWrite = getTime();
	$write_duration_ms = ($endWrite - $startWrite) * 1000;

	// Read from file
	$startRead = getTime();
	$readData = file_get_contents($filename);
	$endRead = getTime();
	$read_duration_ms = ($endRead - $startRead) * 1000;

	// Delete the file
	unlink($filename);

	return [
		'bytes_written' => $bytesWritten,
		'write_time_ms' => $write_duration_ms,
		'read_time_ms' => $read_duration_ms
	];
}

// Execute benchmarks
$cpu = cpuBenchmark(30);
$memory = memoryBenchmark();
$io = ioBenchmark();

// Output the summary in plain text
echo "Summary\n";
echo "=======\n";
echo "CPU Benchmark:\n";
echo "  - Fibonacci(30) = {$cpu['result']}\n";
echo "  - Duration: " . number_format($cpu['time_ms'], 2) . " ms\n\n";

echo "Memory Benchmark:\n";
echo "  - Peak Memory Used: " . number_format($memory['memory_used_mb'], 2) . " MB\n";
echo "  - Duration: " . number_format($memory['time_ms'], 2) . " ms\n\n";

echo "IO Benchmark:\n";
echo "  - Bytes Written: {$io['bytes_written']} bytes\n";
echo "  - Write Duration: " . number_format($io['write_time_ms'], 2) . " ms\n";
echo "  - Read Duration: " . number_format($io['read_time_ms'], 2) . " ms\n";
