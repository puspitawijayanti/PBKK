using System;
using System.Collections.Generic;
using System.Linq;
using System.Windows;
using System.Windows.Controls;

namespace StudentRegistrationApp
{
    public partial class MainWindow : Window
    {

        private List<Student> daftarMahasiswa = new List<Student>();

        private Student sedangDiedit = null;

        public MainWindow()
        {
            InitializeComponent();
            RefreshList();
        }

        private void BtnSimpan_Click(object sender, RoutedEventArgs e)
        {
            string nim = txtNim.Text.Trim();
            string nama = txtNama.Text.Trim();
            string alamat = txtAlamat.Text.Trim();
            string telepon = txtTelepon.Text.Trim();

            // --- Validasi ---
            if (string.IsNullOrWhiteSpace(nim))
            {
                MessageBox.Show("NIM harus diisi!");
                txtNim.Focus();
                return;
            }

            if (!nim.All(char.IsDigit))
            {
                MessageBox.Show("NIM hanya boleh berisi angka!");
                txtNim.Focus();
                return;
            }

            if (sedangDiedit == null &&
                daftarMahasiswa.Any(m => m.Nim == nim))
            {
                MessageBox.Show("NIM sudah terdaftar!");
                txtNim.Focus();
                return;
            }

            if (string.IsNullOrWhiteSpace(nama))
            {
                MessageBox.Show("Nama harus diisi!");
                txtNama.Focus();
                return;
            }

            if (cmbProdi.SelectedItem == null)
            {
                MessageBox.Show("Pilih program studi!");
                return;
            }

            if (rbLaki.IsChecked != true &&
                rbPerempuan.IsChecked != true)
            {
                MessageBox.Show("Pilih jenis kelamin!");
                return;
            }

            if (dpTanggalLahir.SelectedDate == null)
            {
                MessageBox.Show("Pilih tanggal lahir!");
                return;
            }

            if (dpTanggalLahir.SelectedDate.Value > DateTime.Today)
            {
                MessageBox.Show("Tanggal lahir tidak boleh di masa depan!");
                return;
            }

            if (string.IsNullOrWhiteSpace(alamat))
            {
                MessageBox.Show("Alamat harus diisi!");
                txtAlamat.Focus();
                return;
            }

            if (string.IsNullOrWhiteSpace(telepon))
            {
                MessageBox.Show("Nomor telepon harus diisi!");
                txtTelepon.Focus();
                return;
            }

            if (!telepon.All(char.IsDigit) ||
                telepon.Length < 10 || telepon.Length > 13)
            {
                MessageBox.Show(
                    "Nomor telepon harus 10-13 digit angka!");
                txtTelepon.Focus();
                return;
            }


            string prodi = "";
            if (cmbProdi.SelectedItem is ComboBoxItem item)
            {
                prodi = item.Content.ToString();
            }

            string jenisKelamin = "";
            if (rbLaki.IsChecked == true)
            {
                jenisKelamin = "Laki-laki";
            }
            else if (rbPerempuan.IsChecked == true)
            {
                jenisKelamin = "Perempuan";
            }

            DateTime tanggalLahir = dpTanggalLahir.SelectedDate.Value;

   
            if (sedangDiedit == null)
            {
   
                Student baru = new Student
                {
                    Nim = nim,
                    Nama = nama,
                    Prodi = prodi,
                    JenisKelamin = jenisKelamin,
                    TanggalLahir = tanggalLahir,
                    Alamat = alamat,
                    NoTelepon = telepon
                };

                daftarMahasiswa.Add(baru);

                MessageBox.Show(
                    "Data mahasiswa berhasil disimpan!",
                    "Informasi",
                    MessageBoxButton.OK,
                    MessageBoxImage.Information);
            }
            else
            {
           
                sedangDiedit.Nama = nama;
                sedangDiedit.Prodi = prodi;
                sedangDiedit.JenisKelamin = jenisKelamin;
                sedangDiedit.TanggalLahir = tanggalLahir;
                sedangDiedit.Alamat = alamat;
                sedangDiedit.NoTelepon = telepon;

                MessageBox.Show(
                    "Data mahasiswa berhasil diperbarui!",
                    "Informasi",
                    MessageBoxButton.OK,
                    MessageBoxImage.Information);
            }

            RefreshList();
            ResetForm();
        }

      
        private void BtnReset_Click(object sender, RoutedEventArgs e)
        {
            ResetForm();
        }

       
        private void BtnEdit_Click(object sender, RoutedEventArgs e)
        {
            if (lstMahasiswa.SelectedItem is Student terpilih)
            {
                sedangDiedit = terpilih;

                txtNim.Text = terpilih.Nim;
                txtNim.IsEnabled = false; 
                txtNama.Text = terpilih.Nama;

                foreach (ComboBoxItem cb in cmbProdi.Items)
                {
                    if (cb.Content.ToString() == terpilih.Prodi)
                    {
                        cmbProdi.SelectedItem = cb;
                        break;
                    }
                }

                rbLaki.IsChecked = terpilih.JenisKelamin == "Laki-laki";
                rbPerempuan.IsChecked = terpilih.JenisKelamin == "Perempuan";

                dpTanggalLahir.SelectedDate = terpilih.TanggalLahir;
                txtAlamat.Text = terpilih.Alamat;
                txtTelepon.Text = terpilih.NoTelepon;

                btnSimpan.Content = "Update";
                txtNama.Focus();
            }
            else
            {
                MessageBox.Show("Pilih data yang ingin diedit!");
            }
        }

        private void BtnHapus_Click(object sender, RoutedEventArgs e)
        {
            if (lstMahasiswa.SelectedItem is Student terpilih)
            {
                MessageBoxResult konfirmasi = MessageBox.Show(
                    "Yakin ingin menghapus data ini?",
                    "Konfirmasi",
                    MessageBoxButton.YesNo,
                    MessageBoxImage.Question);

                if (konfirmasi == MessageBoxResult.Yes)
                {
                    daftarMahasiswa.Remove(terpilih);

                    
                    if (sedangDiedit == terpilih)
                    {
                        ResetForm();
                    }

                    RefreshList();
                }
            }
            else
            {
                MessageBox.Show("Pilih data yang ingin dihapus!");
            }
        }


        private void TxtCari_TextChanged(object sender, TextChangedEventArgs e)
        {
            RefreshList();
        }

        
        private void RefreshList()
        {
            if (lstMahasiswa == null || txtCounter == null) return;

            string kataKunci = txtCari.Text.Trim().ToLower();

            List<Student> hasil = daftarMahasiswa
                .Where(m => m.Nim.ToLower().Contains(kataKunci) ||
                            m.Nama.ToLower().Contains(kataKunci))
                .ToList();

            lstMahasiswa.ItemsSource = null;
            lstMahasiswa.ItemsSource = hasil;

            txtCounter.Text =
                $"Jumlah Mahasiswa: {daftarMahasiswa.Count}";
        }

        // Kosongkan form dan kembali ke mode tambah
        private void ResetForm()
        {
            txtNim.Clear();
            txtNim.IsEnabled = true;
            txtNama.Clear();
            cmbProdi.SelectedIndex = -1;
            rbLaki.IsChecked = false;
            rbPerempuan.IsChecked = false;
            dpTanggalLahir.SelectedDate = null;
            txtAlamat.Clear();
            txtTelepon.Clear();

            sedangDiedit = null;
            btnSimpan.Content = "Simpan";

            txtNim.Focus();
        }
    }
}