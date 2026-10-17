package com.kpriet.portal.model;

import java.io.Serializable;
import java.math.BigDecimal;
import java.sql.Date;
import java.sql.Timestamp;

/**
 * Student JavaBean matching the shared `students` MySQL table.
 */
public class Student implements Serializable {
    private static final long serialVersionUID = 1L;

    private int id;
    private String fullName;
    private String rollNumber;
    private String email;
    private String phone;
    private String altPhone;
    private Date dateOfBirth;
    private String gender;
    private String nationality;
    private String address;
    private String city;
    private String pincode;

    // Academic
    private String sslcSchool;
    private Integer sslcYear;
    private BigDecimal sslcPercentage;
    private String hscSchool;
    private Integer hscYear;
    private BigDecimal hscPercentage;
    private String hscStream;
    private BigDecimal mathsPercentage;

    // Course & admission
    private String course;
    private String alternativeCourse;
    private String admissionCategory;
    private String admissionStatus;
    private String portalPasswordHash;

    private Timestamp createdAt;
    private Timestamp updatedAt;

    public Student() {}

    // Getters and Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public String getFullName() { return fullName; }
    public void setFullName(String fullName) { this.fullName = fullName; }

    public String getRollNumber() { return rollNumber; }
    public void setRollNumber(String rollNumber) { this.rollNumber = rollNumber; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public String getPhone() { return phone; }
    public void setPhone(String phone) { this.phone = phone; }

    public String getAltPhone() { return altPhone; }
    public void setAltPhone(String altPhone) { this.altPhone = altPhone; }

    public Date getDateOfBirth() { return dateOfBirth; }
    public void setDateOfBirth(Date dateOfBirth) { this.dateOfBirth = dateOfBirth; }

    public String getGender() { return gender; }
    public void setGender(String gender) { this.gender = gender; }

    public String getNationality() { return nationality; }
    public void setNationality(String nationality) { this.nationality = nationality; }

    public String getAddress() { return address; }
    public void setAddress(String address) { this.address = address; }

    public String getCity() { return city; }
    public void setCity(String city) { this.city = city; }

    public String getPincode() { return pincode; }
    public void setPincode(String pincode) { this.pincode = pincode; }

    public String getSslcSchool() { return sslcSchool; }
    public void setSslcSchool(String sslcSchool) { this.sslcSchool = sslcSchool; }

    public Integer getSslcYear() { return sslcYear; }
    public void setSslcYear(Integer sslcYear) { this.sslcYear = sslcYear; }

    public BigDecimal getSslcPercentage() { return sslcPercentage; }
    public void setSslcPercentage(BigDecimal sslcPercentage) { this.sslcPercentage = sslcPercentage; }

    public String getHscSchool() { return hscSchool; }
    public void setHscSchool(String hscSchool) { this.hscSchool = hscSchool; }

    public Integer getHscYear() { return hscYear; }
    public void setHscYear(Integer hscYear) { this.hscYear = hscYear; }

    public BigDecimal getHscPercentage() { return hscPercentage; }
    public void setHscPercentage(BigDecimal hscPercentage) { this.hscPercentage = hscPercentage; }

    public String getHscStream() { return hscStream; }
    public void setHscStream(String hscStream) { this.hscStream = hscStream; }

    public BigDecimal getMathsPercentage() { return mathsPercentage; }
    public void setMathsPercentage(BigDecimal mathsPercentage) { this.mathsPercentage = mathsPercentage; }

    public String getCourse() { return course; }
    public void setCourse(String course) { this.course = course; }

    public String getAlternativeCourse() { return alternativeCourse; }
    public void setAlternativeCourse(String alternativeCourse) { this.alternativeCourse = alternativeCourse; }

    public String getAdmissionCategory() { return admissionCategory; }
    public void setAdmissionCategory(String admissionCategory) { this.admissionCategory = admissionCategory; }

    public String getAdmissionStatus() { return admissionStatus; }
    public void setAdmissionStatus(String admissionStatus) { this.admissionStatus = admissionStatus; }

    public String getPortalPasswordHash() { return portalPasswordHash; }
    public void setPortalPasswordHash(String portalPasswordHash) { this.portalPasswordHash = portalPasswordHash; }

    public Timestamp getCreatedAt() { return createdAt; }
    public void setCreatedAt(Timestamp createdAt) { this.createdAt = createdAt; }

    public Timestamp getUpdatedAt() { return updatedAt; }
    public void setUpdatedAt(Timestamp updatedAt) { this.updatedAt = updatedAt; }
}
