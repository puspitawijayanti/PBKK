using System;

namespace StudentRegistrationApp
{
    public class Student
    {
        public string Nim { get; set; } = "";
        public string Nama { get; set; } = "";
        public string Prodi { get; set; } = "";
        public string JenisKelamin { get; set; } = "";
        public DateTime TanggalLahir { get; set; }
        public string Alamat { get; set; } = "";
        public string NoTelepon { get; set; } = "";

        // Teks yang tampil di ListBox
        public override string ToString()
        {
            return $"{Nim} | {Nama} | {Prodi} | {JenisKelamin} | " +
                   $"{TanggalLahir:dd/MM/yyyy} | {NoTelepon}";
        }
    }
}