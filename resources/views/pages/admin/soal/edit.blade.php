@extends('layouts.master')

@section('title', 'Edit Soal #' . $soal->nomor_soal)

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Soal</h3>
                            <div class="card-tools">
                                <a href="{{ route('master.soal.index.encrypted', \App\Services\UrlEncryptionService::encryptId($soal->id_ujian)) }}" class="btn btn-default btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('master.soal.update.encrypted', App\Services\UrlEncryptionService::encryptId($soal->id)) }}" method="POST" id="soalForm">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="nomor_soal">Nomor Soal <span class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('nomor_soal') is-invalid @enderror"
                                                id="nomor_soal" name="nomor_soal"
                                                value="{{ old('nomor_soal', $soal->nomor_soal) }}" placeholder="Nomor soal"
                                                min="1" required>
                                            @error('nomor_soal')
                                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="tipe">Tipe Soal <span class="text-danger">*</span></label>
                                            <select class="form-control @error('tipe') is-invalid @enderror"
                                                id="tipe" name="tipe" required onchange="toggleJenisSoal()">
                                                <option value="">Pilih Jenis Soal</option>
                                                <option value="pilihan_ganda" {{ old('tipe', $soal->tipe) == 'pilihan_ganda' ? 'selected' : '' }}>
                                                    Pilihan Ganda</option>
                                                <option value="essay" {{ old('tipe', $soal->tipe) == 'essay' ? 'selected' : '' }}>
                                                    Essay</option>
                                            </select>
                                            @error('tipe')
                                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="teks_soal">Pertanyaan <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('teks_soal') is-invalid @enderror" id="teks_soal" name="teks_soal"
                                                rows="4" placeholder="Masukkan pertanyaan" required>{{ old('teks_soal', $soal->teks_soal) }}</textarea>
                                            @error('teks_soal')
                                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Essay Section -->
                                <div id="essaySection" style="display: {{ old('tipe', $soal->tipe) == 'essay' ? 'block' : 'none' }};">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="kunci_jawaban">Kunci Jawaban</label>
                                                <textarea class="form-control @error('kunci_jawaban') is-invalid @enderror" id="kunci_jawaban" name="kunci_jawaban"
                                                    rows="3" placeholder="Masukkan kunci jawaban untuk soal essay">{{ old('kunci_jawaban', $soal->kunci_jawaban) }}</textarea>
                                                @error('kunci_jawaban')
                                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pilihan Ganda Section -->
                                <div id="pilihanGandaSection" style="display: {{ old('tipe', $soal->tipe) == 'pilihan_ganda' ? 'block' : 'none' }};">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Pilihan Jawaban</h5>
                                        <button type="button" class="btn btn-success btn-sm" onclick="addPilihan()">
                                            <i class="fas fa-plus"></i> Tambah Pilihan
                                        </button>
                                    </div>

                                    <div id="pilihanContainer">
                                        @if($soal->tipe == 'pilihan_ganda' && $soal->pilihan->count() > 0)
                                            @foreach($soal->pilihan as $index => $pilihan)
                                                <div class="pilihan-item mb-3">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label>Pilihan {{ strtoupper(chr(65 + $index)) }} <span class="text-danger">*</span></label>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">{{ strtoupper(chr(65 + $index)) }}</span>
                                                                    </div>
                                                                    <input type="text" class="form-control pilihan-text"
                                                                        name="pilihan[{{ $index }}][text]"
                                                                        value="{{ old('pilihan.'.$index.'.text', $pilihan->teks_pilihan) }}"
                                                                        placeholder="Masukkan pilihan {{ strtoupper(chr(65 + $index)) }}" required>
                                                                    <div class="input-group-append">
                                                                        <div class="input-group-text">
                                                                            <input type="radio" name="correct_answer" value="{{ $index }}"
                                                                                {{ (old('pilihan.'.$index.'.is_benar', $pilihan->is_benar)) ? 'checked' : '' }}> Benar
                                                                        </div>
                                                                        @if($index >= 2)
                                                                        <div class="input-group-text">
                                                                            <button data-toggle="tooltip" data-placement="top" title="Hapus Pilihan" type="button" class="btn btn-danger btn-xs" onclick="removePilihan(this)">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                    <input type="hidden" name="pilihan[{{ $index }}][id]" value="{{ $pilihan->id }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="alert alert-info mt-3">
                                        <small><i class="fas fa-info-circle"></i>
                                        Minimal 2 pilihan jawaban. Pilih salah satu sebagai jawaban benar.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('master.soal.index.encrypted', \App\Services\UrlEncryptionService::encryptId($soal->id_ujian)) }}" class="btn btn-default">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        let pilihanCount = {{ $soal->pilihan->count() ?? 0 }};

        function addPilihan() {
            const container = document.getElementById('pilihanContainer');
            const huruf = String.fromCharCode(65 + pilihanCount); // A, B, C, D, E, F, etc.

            const pilihanDiv = document.createElement('div');
            pilihanDiv.className = 'pilihan-item mb-3';
            pilihanDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Pilihan ${huruf} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">${huruf}</span>
                                </div>
                                <input type="text" class="form-control pilihan-text"
                                    name="pilihan[${pilihanCount}][text]"
                                    placeholder="Masukkan pilihan ${huruf}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="radio" name="correct_answer" value="${pilihanCount}"> Benar
                                    </div>
                                    ${pilihanCount >= 2 ? `
                                    <div class="input-group-text">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removePilihan(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(pilihanDiv);
            pilihanCount++;
        }

        function removePilihan(button) {
            const pilihanItem = button.closest('.pilihan-item');
            pilihanItem.remove();

            // Update indeks dan huruf pilihan
            updatePilihanIndices();
        }

        function updatePilihanIndices() {
            const pilihanItems = document.querySelectorAll('.pilihan-item');
            pilihanCount = 0;

            pilihanItems.forEach((item, index) => {
                const huruf = String.fromCharCode(65 + index);
                const input = item.querySelector('.pilihan-text');
                const radio = item.querySelector('input[name="correct_answer"]');
                const label = item.querySelector('label');
                const span = item.querySelector('.input-group-text');

                if (input) {
                    input.name = `pilihan[${index}][text]`;
                    input.placeholder = `Masukkan pilihan ${huruf}`;
                }

                if (radio) {
                    radio.value = index;
                }

                if (label) {
                    label.innerHTML = `Pilihan ${huruf} <span class="text-danger">*</span>`;
                }

                if (span && !span.querySelector('button') && !span.querySelector('input')) {
                    span.textContent = huruf;
                }

                // Update tombol hapus
                const deleteButton = item.querySelector('button');
                if (deleteButton && index < 2) {
                    deleteButton.style.display = 'none';
                } else if (deleteButton) {
                    deleteButton.style.display = 'inline-block';
                }

                pilihanCount = index + 1;
            });
        }

        function toggleJenisSoal() {
            const jenisSoal = document.getElementById('tipe').value;
            const essaySection = document.getElementById('essaySection');
            const pilihanGandaSection = document.getElementById('pilihanGandaSection');

            if (jenisSoal === 'pilihan_ganda') {
                essaySection.style.display = 'none';
                pilihanGandaSection.style.display = 'block';
                document.getElementById('kunci_jawaban').required = false;

                // Tambahkan 2 pilihan default jika belum ada
                if (document.querySelectorAll('.pilihan-item').length === 0) {
                    addPilihan(); // Pilihan A
                    addPilihan(); // Pilihan B
                }
            } else if (jenisSoal === 'essay') {
                essaySection.style.display = 'block';
                pilihanGandaSection.style.display = 'none';
                document.getElementById('kunci_jawaban').required = false;
            } else {
                essaySection.style.display = 'none';
                pilihanGandaSection.style.display = 'none';
            }
        }

        // Handle form submission
        document.getElementById('soalForm').addEventListener('submit', function(e) {
            const jenisSoal = document.getElementById('jenis_soal').value;

            if (jenisSoal === 'pilihan_ganda') {
                const correctAnswer = document.querySelector('input[name="correct_answer"]:checked');
                if (!correctAnswer) {
                    e.preventDefault();
                    alert('Pilih salah satu pilihan sebagai jawaban benar!');
                    return false;
                }
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleJenisSoal();
        });
    </script>
@endpush